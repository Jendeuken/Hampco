<?php


include ('dbconnect.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    public function StockOut($user_id, $raw_used, $raw_qty)
    {
        // Step 1: Get current rm_quantity
        $query = $this->conn->prepare("SELECT rm_quantity FROM raw_materials WHERE id = ?");
        $query->bind_param("i", $raw_used);
        $query->execute();
        $query->bind_result($current_qty);
        $query->fetch();
        $query->close();

        // Step 2: Subtract quantity
        $new_qty = $current_qty - $raw_qty;
        if ($new_qty < 0) {
            return false; // Prevent negative stock
        }

        // Step 3: Update rm_quantity
        $updateQty = $this->conn->prepare("UPDATE raw_materials SET rm_quantity = ? WHERE id = ?");
        $updateQty->bind_param("di", $new_qty, $raw_used);
        $resultQty = $updateQty->execute();
        $updateQty->close();

        if (!$resultQty) {
            return false;
        }

        $change_log = sprintf("%.3f -> %.3f", $current_qty, $new_qty);
        $insertLog = $this->conn->prepare("INSERT INTO stock_history (stock_raw_id,stock_user_type, stock_type,stock_outQty, stock_changes, stock_user_id) VALUES (?,'member', 'Stock Out',?, ?, ?)");
        $insertLog->bind_param("idsi", $raw_used, $raw_qty, $change_log, $user_id);
        $resultLog = $insertLog->execute();
        $insertLog->close();

        return $resultLog;
    }

    public function get_raw_materials_details($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM raw_materials WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();  
        $stmt->close();
        return $data;
    }

    public function fetch_all_materials() {
        $query = $this->conn->prepare("SELECT * FROM `raw_materials`");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }

    public function check_account($id, $type = 'member') {
        $id = intval($id);
        $table = ($type === 'admin') ? 'user_admin' : 'user_member';
        
        $query = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();
        return $items;
    }
}