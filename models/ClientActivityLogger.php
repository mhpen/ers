<?php
class ClientActivityLogger {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function log($clientId, $action, $description) {
        $stmt = $this->conn->prepare("
            INSERT INTO activity_logs (actor_type, actor_id, action, description) 
            VALUES ('client', ?, ?, ?)
        ");
        
        return $stmt->execute([$clientId, $action, $description]);
    }

    public function getClientActivities($clientId, $limit = 50) {
        $stmt = $this->conn->prepare("
            SELECT * FROM activity_logs 
            WHERE actor_type = 'client' AND actor_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$clientId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 