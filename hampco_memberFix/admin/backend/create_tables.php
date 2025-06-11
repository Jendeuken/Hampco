<?php
require_once "class.php";

$db = new global_class();

try {
    // Create sql directory if it doesn't exist
    if (!file_exists(__DIR__ . '/sql')) {
        mkdir(__DIR__ . '/sql', 0777, true);
    }

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/sql/create_task_assignments.sql');
    
    // Execute the SQL
    if ($db->conn->multi_query($sql)) {
        echo "Task assignments table created successfully\n";
    } else {
        throw new Exception("Error creating table: " . $db->conn->error);
    }

    // Clear any remaining results
    while ($db->conn->more_results() && $db->conn->next_result());

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 