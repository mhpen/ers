<?php
require_once 'BaseController.php';
require_once '../../models/Registration.php';

class RegistrationController extends BaseController {
    private $registrationModel;

    public function __construct() {
        parent::__construct();
        $this->registrationModel = new Registration($this->conn);
    }

    public function getEventRegistrations($eventId) {
        // Verify the event belongs to this client
        if (!$this->verifyEventOwnership($eventId)) {
            $this->redirect('../client/events.php', 'Unauthorized access', 'error');
        }

        return $this->registrationModel->getByEventId($eventId);
    }

    private function verifyEventOwnership($eventId) {
        $stmt = $this->conn->prepare("
            SELECT 1 FROM events 
            WHERE id = ? AND client_id = ?
        ");
        $stmt->execute([$eventId, $this->clientId]);
        return $stmt->fetchColumn() !== false;
    }
} 