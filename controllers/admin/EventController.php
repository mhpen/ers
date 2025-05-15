<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('admin');

require_once '../../config/config.php';
require_once '../../models/Event.php';
require_once '../../models/ActivityLogger.php';

class EventController {
    private $conn;
    private $eventModel;
    private $activityLogger;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->eventModel = new Event($conn);
        $this->activityLogger = new ActivityLogger($conn);
    }

    public function getFilteredEvents($status = 'all', $category = 'all', $type = 'all', $sort = 'newest') {
        return $this->eventModel->getFilteredEvents($status, $category, $type, $sort);
    }

    public function getCategories() {
        return $this->eventModel->getCategories();
    }

    public function getEventTypes() {
        return $this->eventModel->getEventTypes();
    }

    public function getEventDetails($eventId) {
        try {
            $sql = "SELECT e.*, 
                    c.name as category_name, 
                    t.name as type_name,
                    cl.name as client_name,
                    cl.email as client_email
                    FROM events e
                    LEFT JOIN categories c ON e.category_id = c.id
                    LEFT JOIN event_types t ON e.type_id = t.id
                    LEFT JOIN clients cl ON e.client_id = cl.id
                    WHERE e.id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $eventId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event details: " . $e->getMessage());
            return null;
        }
    }

    public function approveEvent($eventId) {
        try {
            $event = $this->getEventDetails($eventId);
            if (!$event) {
                throw new Exception("Event not found");
            }

            if ($this->eventModel->updateStatus($eventId, 'approved')) {
                // Log the activity
                $success = $this->activityLogger->log(
                    $_SESSION['admin']['id'],
                    'approve_event',
                    "Approved event: {$event['title']} (ID: {$eventId})"
                );

                if (!$success) {
                    error_log("Failed to log activity for event approval");
                }

                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error approving event: " . $e->getMessage());
            return false;
        }
    }

    public function rejectEvent($eventId, $reason) {
        try {
            // Debug logging
            error_log("Starting reject event. EventID: $eventId, Reason: $reason");
            error_log("Admin ID from session: " . print_r($_SESSION['admin'], true));

            $event = $this->getEventDetails($eventId);
            if (!$event) {
                error_log("Event not found for ID: $eventId");
                throw new Exception("Event not found");
            }

            error_log("Event found: " . print_r($event, true));

            if ($this->eventModel->updateStatus($eventId, 'rejected')) {
                // Log the activity with rejection reason
                $logData = [
                    'actor_id' => $_SESSION['admin']['id'],
                    'action' => 'reject_event',
                    'description' => "Rejected event: {$event['title']} (ID: {$eventId}). Reason: {$reason}"
                ];
                error_log("Attempting to log activity with data: " . print_r($logData, true));

                $success = $this->activityLogger->log(
                    $_SESSION['admin']['id'],
                    'reject_event',
                    "Rejected event: {$event['title']} (ID: {$eventId}). Reason: {$reason}"
                );

                error_log("Activity logging result: " . ($success ? "Success" : "Failed"));

                if (!$success) {
                    error_log("Failed to log activity for event rejection");
                }

                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error rejecting event: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getAllEvents($page = 1, $perPage = 10) {
        try {
            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM events";
            $countStmt = $this->conn->query($countSql);
            $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Calculate offset
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT 
                    e.*,
                    c.name as client_name,
                    cat.name as category_name,
                    et.name as type_name
                    FROM events e
                    LEFT JOIN clients c ON e.client_id = c.id
                    LEFT JOIN categories cat ON e.category_id = cat.id
                    LEFT JOIN event_types et ON e.type_id = et.id
                    ORDER BY e.created_at DESC
                    LIMIT :limit OFFSET :offset";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'events' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalCount,
                'pages' => ceil($totalCount / $perPage),
                'current_page' => $page
            ];
        } catch (PDOException $e) {
            error_log("Error fetching all events: " . $e->getMessage());
            return [
                'events' => [],
                'total' => 0,
                'pages' => 1,
                'current_page' => 1
            ];
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new EventController();
    $eventId = $_POST['event_id'] ?? null;
    $action = $_POST['action'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (!$eventId) {
        header('Location: ../../views/admin/events.php?error=Invalid event ID');
        exit();
    }

    $success = false;
    switch ($action) {
        case 'approve':
            $success = $controller->approveEvent($eventId);
            $message = $success ? 'Event approved successfully' : 'Failed to approve event';
            break;
        case 'reject':
            $success = $controller->rejectEvent($eventId, $reason);
            $message = $success ? 'Event rejected successfully' : 'Failed to reject event';
            break;
        default:
            header('Location: ../../views/admin/events.php?error=Invalid action');
            exit();
    }

    $redirectParam = $success ? 'success' : 'error';
    header("Location: ../../views/admin/events.php?{$redirectParam}=" . urlencode($message));
    exit();
} 