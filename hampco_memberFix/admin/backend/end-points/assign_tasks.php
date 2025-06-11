<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/hampco_memberFix/admin/backend/class.php";

$db = new global_class();
$response = array();

try {
    // Get the production identifier (either code or ID) and member IDs
    $identifier = $_POST['identifier'];
    $knotter_ids = !empty($_POST['knotter_id']) ? $_POST['knotter_id'] : []; // Changed to handle array
    $warper_id = !empty($_POST['warper_id']) ? $_POST['warper_id'] : null;
    $weaver_id = !empty($_POST['weaver_id']) ? $_POST['weaver_id'] : null;

    // Get estimated times and deadlines with default values
    $knotter_estimated_time = isset($_POST['knotter_estimated_time']) ? intval($_POST['knotter_estimated_time']) : 0;
    $warper_estimated_time = isset($_POST['warper_estimated_time']) ? intval($_POST['warper_estimated_time']) : 0;
    $weaver_estimated_time = isset($_POST['weaver_estimated_time']) ? intval($_POST['weaver_estimated_time']) : 0;

    $knotter_deadline = !empty($_POST['knotter_deadline']) ? $_POST['knotter_deadline'] : null;
    $warper_deadline = !empty($_POST['warper_deadline']) ? $_POST['warper_deadline'] : null;
    $weaver_deadline = !empty($_POST['weaver_deadline']) ? $_POST['weaver_deadline'] : null;

    // Start transaction
    $db->conn->begin_transaction();

    // Get the prod_line_id - handle both production_code and direct prod_line_id
    if (is_numeric($identifier)) {
        $query = "SELECT prod_line_id FROM production_line WHERE prod_line_id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param("i", $identifier);
    } else {
        $query = "SELECT prod_line_id FROM production_line WHERE production_code = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param("s", $identifier);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Production item not found");
    }
    
    $row = $result->fetch_assoc();
    $prod_line_id = $row['prod_line_id'];

    // Check if tasks are already assigned
    $check_query = "SELECT COUNT(*) as count FROM task_assignments WHERE prod_line_id = ?";
    $check_stmt = $db->conn->prepare($check_query);
    $check_stmt->bind_param("i", $prod_line_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    
    if ($check_result['count'] > 0) {
        throw new Exception("Tasks are already assigned to this production item");
    }

    // Prepare task assignments
    $assignments = array();
    
    // Handle multiple knotters
    if (!empty($knotter_ids)) {
        // Use array_unique to prevent duplicate knotter IDs
        $knotter_ids = array_unique($knotter_ids);
        foreach ($knotter_ids as $knotter_id) {
            if (!empty($knotter_id)) {
                $assignments[] = array(
                    'id' => $knotter_id,
                    'role' => 'knotter',
                    'estimated_time' => $knotter_estimated_time,
                    'deadline' => $knotter_deadline
                );
            }
        }
    }
    
    if ($warper_id) {
        $assignments[] = array(
            'id' => $warper_id,
            'role' => 'warper',
            'estimated_time' => $warper_estimated_time,
            'deadline' => $warper_deadline
        );
    }
    if ($weaver_id) {
        $assignments[] = array(
            'id' => $weaver_id,
            'role' => 'weaver',
            'estimated_time' => $weaver_estimated_time,
            'deadline' => $weaver_deadline
        );
    }

    if (empty($assignments)) {
        throw new Exception("No members selected for assignment");
    }

    // Check for duplicate assignments
    $unique_assignments = [];
    foreach ($assignments as $assignment) {
        $key = $assignment['id'] . '-' . $assignment['role'];
        if (!isset($unique_assignments[$key])) {
            $unique_assignments[$key] = $assignment;
        }
    }
    $assignments = array_values($unique_assignments);

    // Insert task assignments
    $insert_query = "INSERT INTO task_assignments (prod_line_id, member_id, role, status, estimated_time, deadline) 
                    VALUES (?, ?, ?, 'pending', ?, ?)";
    $insert_stmt = $db->conn->prepare($insert_query);

    foreach ($assignments as $assignment) {
        $insert_stmt->bind_param("iisis", 
            $prod_line_id, 
            $assignment['id'], 
            $assignment['role'],
            $assignment['estimated_time'],
            $assignment['deadline']
        );
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Error assigning task to " . $assignment['role']);
        }
    }

    // Update production line status
    $update_query = "UPDATE production_line SET status = 'in_progress' WHERE prod_line_id = ?";
    $update_stmt = $db->conn->prepare($update_query);
    $update_stmt->bind_param("i", $prod_line_id);
    $update_stmt->execute();

    // Commit transaction
    $db->conn->commit();

    $response['success'] = true;
    $response['message'] = 'Tasks assigned successfully';

} catch (Exception $e) {
    // Rollback on error
    if (isset($db->conn)) {
        $db->conn->rollback();
    }
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 