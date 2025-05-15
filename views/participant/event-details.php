<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');

require_once '../../models/Event.php';
require_once '../../config/config.php';

class EventDetailsController {
    private $conn;
    private $eventModel;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->eventModel = new Event($conn);
    }

    public function getEventDetails($eventId) {
        try {
            $sql = "SELECT e.*, c.name as category_name, t.name as type_name,
                    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_participants,
                    JSON_UNQUOTE(JSON_EXTRACT(e.location, '$.physical')) as physical_location,
                    JSON_UNQUOTE(JSON_EXTRACT(e.location, '$.virtual')) as virtual_location,
                    cl.name as organizer_name
                    FROM events e
                    LEFT JOIN categories c ON e.category_id = c.id
                    LEFT JOIN event_types t ON e.type_id = t.id
                    LEFT JOIN clients cl ON e.client_id = cl.id
                    WHERE e.id = :event_id
                    AND e.status = 'published'
                    AND e.registration_deadline >= CURRENT_TIMESTAMP";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event details: " . $e->getMessage());
            return null;
        }
    }

    public function checkRegistration($eventId, $participantId) {
        try {
            $sql = "SELECT COUNT(*) FROM registrations 
                    WHERE event_id = :event_id AND participant_id = :participant_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':event_id' => $eventId,
                ':participant_id' => $participantId
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking registration: " . $e->getMessage());
            return false;
        }
    }
}

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    header('Location: participantPage.php');
    exit();
}

$controller = new EventDetailsController();
$event = $controller->getEventDetails($eventId);

if (!$event) {
    header('Location: participantPage.php?error=Event not found or not available');
    exit();
}

$isRegistered = $controller->checkRegistration($eventId, $_SESSION['participant_id']);

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="participantPage.php" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Events
            </a>

            <!-- Event Banner -->
            <?php if ($event['banner']): ?>
                <div class="rounded-lg overflow-hidden mb-8">
                    <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                         class="w-full h-64 object-cover">
                </div>
            <?php endif; ?>

            <!-- Event Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold tracking-tight mb-4">
                    <?php echo htmlspecialchars($event['title']); ?>
                </h1>
                
                <div class="flex flex-wrap gap-4 mb-6">
                    <!-- Event Type Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                <?php echo $event['type_name'] === 'Virtual' ? 'bg-blue-100 text-blue-800' : 
                                      ($event['type_name'] === 'Hybrid' ? 'bg-purple-100 text-purple-800' : 
                                       'bg-green-100 text-green-800'); ?>">
                        <?php echo htmlspecialchars($event['type_name']); ?>
                    </span>

                    <!-- Category Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">
                        <?php echo htmlspecialchars($event['category_name']); ?>
                    </span>

                    <!-- Price Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-medium">
                        <?php echo $event['price'] > 0 ? '₱' . number_format($event['price'], 2) : 'Free'; ?>
                    </span>
                </div>

                <!-- Event Details -->
                <div class="grid gap-4 text-sm text-muted-foreground">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo date('F j, Y g:i A', strtotime($event['event_date'])); ?>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <?php 
                        if ($event['type_name'] === 'Virtual') {
                            echo 'Online Event';
                            if ($event['virtual_location']) {
                                echo ' - Link will be provided after registration';
                            }
                        } else {
                            echo htmlspecialchars($event['physical_location']);
                        }
                        ?>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <?php 
                        $availableSlots = $event['slots'] - ($event['registered_participants'] ?? 0);
                        echo "{$availableSlots} slots available";
                        ?>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Registration deadline: <?php echo date('F j, Y', strtotime($event['registration_deadline'])); ?>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Organized by: <?php echo htmlspecialchars($event['organizer_name']); ?>
                    </div>
                </div>
            </div>

            <!-- Event Description -->
            <div class="prose max-w-none mb-8">
                <h2 class="text-xl font-semibold mb-4">About This Event</h2>
                <div class="text-muted-foreground">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
            </div>

            <!-- Registration Button -->
            <div class="flex justify-center mt-8">
                <?php if ($isRegistered): ?>
                    <button disabled class="btn-secondary px-6 py-3 rounded-md text-sm">
                        Already Registered
                    </button>
                <?php elseif ($availableSlots <= 0): ?>
                    <button disabled class="btn-secondary px-6 py-3 rounded-md text-sm">
                        Event Full
                    </button>
                <?php else: ?>
                    <form action="../../controllers/participant/RegistrationController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn-primary px-6 py-3 rounded-md text-sm">
                            Register for Event
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 