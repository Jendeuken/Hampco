<?php
session_start();
require_once '../../function/config.php';

// Check if the request is POST and contains JSON data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['member_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['taskId']) || !isset($data['completion']) || !isset($data['note'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$taskId = intval($data['taskId']);
$completion = intval($data['completion']);
$note = $data['note'];
$memberId = $_SESSION['member_id'];

// Validate completion percentage
if ($completion < 0 || $completion > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid completion percentage']);
    exit;
}

// Start transaction
$db->conn->begin_transaction();

try {
    // Insert progress update
    $query = "INSERT INTO task_progress (assigned_task_id, progress_note, completion_percentage) 
              VALUES (?, ?, ?)";
    $stmt = $db->conn->prepare($query);
    $stmt->bind_param('isi', $taskId, $note, $completion);
    $stmt->execute();

    // Update task status if completed
    if ($completion === 100) {
        $query = "UPDATE assigned_tasks 
                  SET status = 'Completed', 
                      date_updated = CURRENT_TIMESTAMP 
                  WHERE id = ? AND member_id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param('ii', $taskId, $memberId);
        $stmt->execute();
    }

    // Commit transaction
    $db->conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $db->conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 