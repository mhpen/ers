<?php
require_once 'ActivityLogger.php';

class ClientManager {
    private $conn;
    private $activityLogger;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->activityLogger = new ActivityLogger($conn);
    }

    public function getAllClients() {
        $stmt = $this->conn->prepare("SELECT * FROM clients ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveClient($clientId, $adminId) {
        try {
            $this->conn->beginTransaction();

            // Update client status
            $stmt = $this->conn->prepare("UPDATE clients SET approved = TRUE WHERE id = ?");
            $stmt->execute([$clientId]);

            // Get client details for logging
            $stmt = $this->conn->prepare("SELECT name, organization FROM clients WHERE id = ?");
            $stmt->execute([$clientId]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            // Log the activity
            $this->activityLogger->log(
                'admin',
                $adminId,
                'approve_client',
                "Approved client: {$client['name']} from {$client['organization']}"
            );

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error approving client: " . $e->getMessage());
            return false;
        }
    }

    public function rejectClient($clientId, $adminId) {
        try {
            $this->conn->beginTransaction();

            // Get client details before deletion for logging
            $stmt = $this->conn->prepare("SELECT name, organization FROM clients WHERE id = ?");
            $stmt->execute([$clientId]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            // Delete the client
            $stmt = $this->conn->prepare("DELETE FROM clients WHERE id = ?");
            $stmt->execute([$clientId]);

            // Log the activity
            $this->activityLogger->log(
                'admin',
                $adminId,
                'reject_client',
                "Rejected client: {$client['name']} from {$client['organization']}"
            );

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error rejecting client: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a client by their ID
     * @param int $id The client ID
     * @return array|false The client data or false if not found
     */
    public function getClientById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Suspend a client
     * @param int $id The client ID
     * @param int $adminId The admin performing the action
     * @return bool Whether the operation was successful
     */
    public function suspendClient($id, $adminId) {
        try {
            $this->conn->beginTransaction();

            // Get client details before suspension for logging
            $stmt = $this->conn->prepare("SELECT name, organization FROM clients WHERE id = ?");
            $stmt->execute([$id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            // Update client status
            $stmt = $this->conn->prepare("UPDATE clients SET approved = FALSE WHERE id = ?");
            $stmt->execute([$id]);

            // Log the activity
            $this->activityLogger->log(
                'admin',
                $adminId,
                'suspend_client',
                "Suspended client: {$client['name']} from {$client['organization']}"
            );

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error suspending client: " . $e->getMessage());
            return false;
        }
    }
} 