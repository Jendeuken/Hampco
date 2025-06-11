<?php
header('Content-Type: application/json');
include('../class.php');

$db = new global_class();

if (!isset($_GET['role'])) {
    echo json_encode(['error' => 'Role parameter is required']);
    exit;
}

$role = $_GET['role'];
$valid_roles = ['knotter', 'warper', 'weaver'];

if (!in_array($role, $valid_roles)) {
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

// Get verified members by role
$query = "SELECT id, fullname FROM user_member WHERE role = ? AND status = 1 ORDER BY fullname ASC";
$stmt = $db->conn->prepare($query);
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
$stmt->close(); 