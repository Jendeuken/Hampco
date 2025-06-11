<?php
require_once 'class.php';
$db = new global_class();

try {
    // Drop the index first
    $sql1 = "ALTER TABLE production_line DROP INDEX idx_production_code";
    if ($db->conn->query($sql1) === TRUE) {
        echo "Index dropped successfully<br>";
    } else {
        echo "Error dropping index: " . $db->conn->error . "<br>";
    }

    // Then drop the column
    $sql2 = "ALTER TABLE production_line DROP COLUMN production_code";
    if ($db->conn->query($sql2) === TRUE) {
        echo "Column dropped successfully<br>";
    } else {
        echo "Error dropping column: " . $db->conn->error . "<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 