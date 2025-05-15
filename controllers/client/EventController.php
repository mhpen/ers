<?php
require_once 'BaseController.php';
require_once '../../models/Event.php';

class EventController extends BaseController {
    private $eventModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event($this->conn);
    }

    public function getClientEvents($page = 1, $perPage = 10) {
        $client_id = $_SESSION['client']['id'];
        $offset = ($page - 1) * $perPage;

        try {
            // Get total count
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM events WHERE client_id = ?");
            $stmt->execute([$client_id]);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get paginated events
            $stmt = $this->conn->prepare("
                SELECT e.*, 
                       COUNT(DISTINCT r.id) as registrations_count
                FROM events e
                LEFT JOIN registrations r ON e.id = r.event_id
                WHERE e.client_id = ?
                GROUP BY e.id
                ORDER BY e.event_date DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$client_id, $perPage, $offset]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'events' => $events,
                'total' => $total,
                'totalPages' => ceil($total / $perPage),
                'currentPage' => $page,
                'perPage' => $perPage
            ];
        } catch (PDOException $e) {
            error_log("Error fetching events: " . $e->getMessage());
            return [
                'events' => [],
                'total' => 0,
                'totalPages' => 0,
                'currentPage' => 1,
                'perPage' => $perPage
            ];
        }
    }

    public function createEvent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('../views/client/events.php', 'Invalid request method', 'error');
        }

        $eventData = [
            'client_id' => $this->clientId,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category_id'],
            'type_id' => $_POST['type_id'],
            'event_date' => $_POST['event_date'],
            'registration_deadline' => $_POST['registration_deadline'],
            'location' => $_POST['location'],
            'price' => $_POST['price'],
            'slots' => $_POST['slots'],
            'visibility' => $_POST['visibility']
        ];

        if ($this->eventModel->create($eventData)) {
            $this->activityLogger->log(
                $this->clientId,
                'create_event',
                "Created new event: {$eventData['title']}"
            );
            $this->redirect('../views/client/events.php', 'Event created successfully');
        } else {
            $this->redirect('../views/client/create-event.php', 'Failed to create event', 'error');
        }
    }

    public function getEvent($eventId) {
        $event = $this->eventModel->getById($eventId);
        if (!$event || $event['client_id'] !== $this->clientId) {
            $this->redirect('../views/client/events.php', 'Event not found', 'error');
        }
        return $event;
    }

    public function updateEvent($eventId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('../views/client/events.php', 'Invalid request method', 'error');
        }

        // Handle banner deletion
        if ($_POST['action'] === 'delete_banner') {
            $event = $this->eventModel->getById($eventId);
            if ($event && $event['banner']) {
                $fullPath = '../../public/' . $event['banner'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                $this->eventModel->updateBanner($eventId, null);
            }
            $this->redirect("../views/client/edit-event.php?id=$eventId", 'Banner deleted successfully');
            return;
        }

        // Handle new banner upload
        $bannerPath = null;
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $bannerPath = $this->handleBannerUpload($_FILES['banner']);
            if (!$bannerPath) {
                $this->redirect("../views/client/edit-event.php?id=$eventId", 'Failed to upload banner', 'error');
                return;
            }
            
            // Delete old banner if exists
            $event = $this->eventModel->getById($eventId);
            if ($event && $event['banner']) {
                $fullPath = '../../public/' . $event['banner'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        } else {
            // Keep existing banner
            $bannerPath = $_POST['current_banner'] ?? null;
        }

        $eventData = [
            ':client_id' => $this->clientId,
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
            ':category_id' => $_POST['category_id'],
            ':type_id' => $_POST['type_id'],
            ':event_date' => $_POST['event_date'],
            ':registration_deadline' => $_POST['registration_deadline'],
            ':location' => $_POST['location'],
            ':price' => $_POST['price'],
            ':slots' => $_POST['slots'],
            ':visibility' => $_POST['visibility'],
            ':banner' => $bannerPath
        ];

        if ($this->eventModel->update($eventId, $eventData)) {
            $this->activityLogger->log(
                $this->clientId,
                'update_event',
                "Updated event: {$eventData[':title']}"
            );
            $this->redirect("../views/client/event-details.php?id=$eventId", 'Event updated successfully');
        } else {
            // If update fails and we uploaded a new banner, clean it up
            if ($bannerPath && $bannerPath !== $_POST['current_banner']) {
                $fullPath = '../../public/' . $bannerPath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $this->redirect("../views/client/edit-event.php?id=$eventId", 'Failed to update event', 'error');
        }
    }

    public function getCategories() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public function getEventTypes() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM event_types ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event types: " . $e->getMessage());
            return [];
        }
    }

    private function handleBannerUpload($file) {
        try {
            // Check file size (2MB limit)
            if ($file['size'] > 2 * 1024 * 1024) {
                throw new Exception('File size exceeds 2MB limit');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('event_banner_') . '.' . $extension;
            
            // Create uploads directory if it doesn't exist
            $uploadDir = '../../public/uploads/events/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to move uploaded file');
            }

            return 'uploads/events/' . $filename;
        } catch (Exception $e) {
            error_log("Banner upload error: " . $e->getMessage());
            return false;
        }
    }

    public function handleCreateEvent() {
        // Temporary debugging
        error_log('Form Data: ' . print_r($_POST, true));
        error_log('Files Data: ' . print_r($_FILES, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_POST['action'] !== 'create') {
            $this->redirect('../client/events.php', 'Invalid request', 'error');
        }

        try {
            // Handle banner upload if present
            $bannerPath = null;
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                $bannerPath = $this->handleBannerUpload($_FILES['banner']);
                if (!$bannerPath) {
                    throw new Exception('Failed to upload banner image');
                }
            }

            // Set status based on form submission
            $status = $_POST['status'] === 'draft' ? 'draft' : 'pending';
            
            // Handle meeting link for virtual/hybrid events
            $location = $_POST['location'];
            if (in_array($_POST['type_id'], ['2', '3'])) { // Virtual or Hybrid
                $location = json_encode([
                    'physical' => $_POST['location'] ?? null,
                    'virtual' => $_POST['meeting_link'] ?? null
                ]);
            }

            $eventData = [
                'client_id' => $this->clientId,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category_id' => $_POST['category_id'],
                'type_id' => $_POST['type_id'],
                'event_date' => $_POST['event_date'],
                'registration_deadline' => $_POST['registration_deadline'],
                'location' => $location,
                'price' => $_POST['price'],
                'slots' => $_POST['slots'],
                'max_participants_per_registration' => $_POST['max_participants_per_registration'],
                'visibility' => $_POST['visibility'],
                'status' => $status,
                'banner' => $bannerPath
            ];

            if ($this->eventModel->create($eventData)) {
                $statusMessage = $status === 'draft' ? 'Event saved as draft' : 'Event submitted for review';
                $this->activityLogger->log(
                    $this->clientId,
                    'create_event',
                    "Created new event: {$eventData['title']} ({$status})"
                );
                $this->redirect('/event-registration-system/views/client/events.php', $statusMessage);
            } else {
                if ($bannerPath) {
                    unlink('../../public/' . $bannerPath);
                }
                throw new Exception('Failed to create event');
            }
        } catch (Exception $e) {
            $this->redirect('/event-registration-system/views/client/create-event.php', $e->getMessage(), 'error');
        }
    }

    public function handleUpdateEvent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('../views/client/events.php', 'Invalid request method', 'error');
        }

        try {
            $eventId = $_POST['event_id'];
            $event = $this->getEvent($eventId);

            if (!$event) {
                throw new Exception('Event not found');
            }

            // Handle banner upload if present
            $bannerPath = $_POST['current_banner'] ?? null;
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                $newBannerPath = $this->handleBannerUpload($_FILES['banner']);
                if (!$newBannerPath) {
                    throw new Exception('Failed to upload banner image');
                }
                
                // Delete old banner if exists
                if ($event['banner']) {
                    $fullPath = '../../public/' . $event['banner'];
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
                
                $bannerPath = $newBannerPath;
            }

            // Handle location for virtual/hybrid events
            $location = $_POST['location'];
            if (in_array($_POST['type_id'], ['2', '3'])) { // Virtual or Hybrid
                $location = json_encode([
                    'physical' => $_POST['location'] ?? null,
                    'virtual' => $_POST['meeting_link'] ?? null
                ]);
            }

            $eventData = [
                ':title' => $_POST['title'],
                ':description' => $_POST['description'],
                ':category_id' => $_POST['category_id'],
                ':type_id' => $_POST['type_id'],
                ':event_date' => $_POST['event_date'],
                ':registration_deadline' => $_POST['registration_deadline'],
                ':location' => $location,
                ':price' => $_POST['price'],
                ':slots' => $_POST['slots'],
                ':max_participants_per_registration' => $_POST['max_per_registration'],
                ':visibility' => $_POST['visibility'],
                ':banner' => $bannerPath,
                ':client_id' => $this->clientId
            ];

            if ($this->eventModel->update($eventId, $eventData)) {
                $this->activityLogger->log(
                    $this->clientId,
                    'update_event',
                    "Updated event: {$eventData[':title']}"
                );
                // Fix the redirect path by removing 'controllers' from the URL
                $this->redirect("/event-registration-system/views/client/event-details.php?id=$eventId", 'Event updated successfully');
            } else {
                throw new Exception('Failed to update event');
            }
        } catch (Exception $e) {
            $this->redirect("../views/client/edit-event.php?id=$eventId", $e->getMessage(), 'error');
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $controller = new EventController();
                $controller->handleCreateEvent();
                break;
            case 'update':
                $controller = new EventController();
                $controller->handleUpdateEvent();
                break;
            // Add other cases as needed
        }
    }
}
