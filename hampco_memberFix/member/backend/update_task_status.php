<?php
require_once '../../function/connection.php';
header('Content-Type: application/json');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['taskId']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$taskId = intval($data['taskId']);
$status = $data['status'];

// Validate status
$valid_statuses = ['Pending', 'In Progress', 'Completed'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();

    // Get the production line ID and role for this task
    $getTaskQuery = "SELECT ta.prod_line_id, ta.role, ta.member_id 
                     FROM task_assignments ta 
                     WHERE ta.id = :taskId";
    $stmt = $conn->prepare($getTaskQuery);
    $stmt->bindParam(':taskId', $taskId);
    $stmt->execute();
    $taskInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$taskInfo) {
        throw new Exception('Task not found');
    }

    // Update task status
    $updateQuery = "UPDATE task_assignments 
                   SET status = :status,
                       updated_at = CURRENT_TIMESTAMP 
                   WHERE id = :taskId";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':taskId', $taskId);
    $stmt->execute();

    // Add progress entry
    if ($status === 'completed') {
        $progressNote = ucfirst($taskInfo['role']) . " task completed";
        $insertProgressQuery = "INSERT INTO task_progress 
                              (assigned_task_id, progress_note, completion_percentage) 
                              VALUES (:taskId, :note, 100)";
        $stmt = $conn->prepare($insertProgressQuery);
        $stmt->bindParam(':taskId', $taskId);
        $stmt->bindParam(':note', $progressNote);
        $stmt->execute();

        // Only update production line status if this is a weaver completing their task
        if ($taskInfo['role'] === 'weaver') {
            // Check if all tasks for this production line are completed
            $checkAllCompletedQuery = "SELECT COUNT(*) as total, 
                                     SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                                     FROM task_assignments 
                                     WHERE prod_line_id = :prodLineId";
            $stmt = $conn->prepare($checkAllCompletedQuery);
            $stmt->bindParam(':prodLineId', $taskInfo['prod_line_id']);
            $stmt->execute();
            $completionInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            // If all tasks are completed, update production line status
            if ($completionInfo['total'] == $completionInfo['completed']) {
                $updateProdLineQuery = "UPDATE production_line 
                                      SET status = 'completed' 
                                      WHERE prod_line_id = :prodLineId";
                $stmt = $conn->prepare($updateProdLineQuery);
                $stmt->bindParam(':prodLineId', $taskInfo['prod_line_id']);
                $stmt->execute();
            }
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Error updating task status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating task status: ' . $e->getMessage()]);
} 