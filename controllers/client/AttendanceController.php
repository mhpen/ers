<?php
require_once '../../config/config.php';
require_once '../../helpers/NotificationHelper.php';

class AttendanceController {
    private $conn;
    private $notificationHelper;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->notificationHelper = new NotificationHelper();
    }

    public function checkIn($eventId, $registrationId) {
        try {
            $this->conn->beginTransaction();

            // Verify registration exists and belongs to the event
            $sql = "SELECT r.*, e.title as event_title, p.id as participant_id 
                    FROM registrations r
                    JOIN events e ON r.event_id = e.id
                    JOIN participants p ON r.participant_id = p.id
                    WHERE r.id = ? AND e.id = ? AND r.status = 'confirmed'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$registrationId, $eventId]);
            $registration = $stmt->fetch();

            if (!$registration) {
                throw new Exception("Invalid registration or not confirmed");
            }

            // Check if already checked in
            $sql = "SELECT id FROM attendance WHERE registration_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$registrationId]);
            if ($stmt->fetch()) {
                throw new Exception("Participant already checked in");
            }

            // Record attendance
            $sql = "INSERT INTO attendance (registration_id, check_in_time) VALUES (?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$registrationId]);

            // Create notification
            $notificationData = [
                'recipient_type' => 'participant',
                'recipient_id' => $registration['participant_id'],
                'message' => "You have been checked in for {$registration['event_title']}",
                'is_read' => 0
            ];
            $this->notificationHelper->createNotification($notificationData);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Check-in error: " . $e->getMessage());
            throw $e;
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    if (isset($_POST['action']) && $_POST['action'] === 'check_in') {
        try {
            if (!isset($_POST['event_id']) || !isset($_POST['registration_id'])) {
                throw new Exception("Missing required fields");
            }

            $controller = new AttendanceController();
            $controller->checkIn($_POST['event_id'], $_POST['registration_id']);
            $_SESSION['success'] = "Participant checked in successfully";

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: ../../views/client/check-in.php');
        exit();
    }
} 