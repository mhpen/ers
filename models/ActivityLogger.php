<?php
class ActivityLogger {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function log($actorId, $action, $description, $actorType = 'admin') {
        try {
            error_log("ActivityLogger::log called with: " . print_r([
                'actorId' => $actorId,
                'action' => $action,
                'description' => $description,
                'actorType' => $actorType
            ], true));

            $sql = "INSERT INTO activity_logs (actor_type, actor_id, action, description) 
                    VALUES (:actor_type, :actor_id, :action, :description)";
            
            $stmt = $this->conn->prepare($sql);
            $params = [
                ':actor_type' => $actorType,
                ':actor_id' => $actorId,
                ':action' => $action,
                ':description' => $description
            ];
            
            error_log("Executing SQL with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            error_log("SQL execution result: " . ($result ? "Success" : "Failed"));
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error logging activity: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getActivityLogs($limit = 50) {
        try {
            $sql = "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching activity logs: " . $e->getMessage());
            return [];
        }
    }

    public function getActorLogs($actorType, $actorId, $limit = 50) {
        try {
            $sql = "SELECT * FROM activity_logs 
                    WHERE actor_type = :actor_type AND actor_id = :actor_id 
                    ORDER BY created_at DESC LIMIT :limit";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':actor_type', $actorType, PDO::PARAM_STR);
            $stmt->bindValue(':actor_id', $actorId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching actor logs: " . $e->getMessage());
            return [];
        }
    }
} 