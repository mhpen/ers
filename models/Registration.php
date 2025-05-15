<?php
class Registration {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function updateStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE registrations 
                SET status = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Error updating registration status: " . $e->getMessage());
            return false;
        }
    }
}
?>