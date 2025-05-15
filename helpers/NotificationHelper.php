<?php
class NotificationHelper {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function createNotification($data) {
        try {
            $sql = "INSERT INTO notifications (recipient_type, recipient_id, message, is_read) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['recipient_type'],
                $data['recipient_id'],
                $data['message'],
                $data['is_read']
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }
} 