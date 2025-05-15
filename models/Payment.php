<?php
class Payment {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT p.*, r.id as registration_id 
                FROM payments p
                JOIN registrations r ON p.registration_id = r.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                } else {
                    $fields[] = "$key = NULL";
                }
            }
            
            $values[] = $id;
            $sql = "UPDATE payments SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error updating payment: " . $e->getMessage());
            return false;
        }
    }
}
?>