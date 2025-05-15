<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');

require_once '../../controllers/client/EventController.php';
$eventController = new EventController();

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    header('Location: events.php?error=Event ID not provided');
    exit();
}

$event = $eventController->getEvent($eventId);
include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <main class="ml-64 p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="events.php" class="text-sm text-muted-foreground hover:text-foreground flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Events
            </a>
        </div>

        <!-- Event Banner -->
        <div class="rounded-lg border bg-card overflow-hidden mb-8">
            <div class="aspect-[21/9] relative bg-muted">
                <?php if ($event['banner']): ?>
                    <img 
                        src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                        alt="<?php echo htmlspecialchars($event['title']); ?>"
                        class="object-cover w-full h-full"
                    >
                <?php else: ?>
                    <div class="flex items-center justify-center h-full bg-muted">
                        <svg class="h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Event Information -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-lg border bg-card p-6">
                    <h1 class="text-2xl font-bold tracking-tight mb-2"><?php echo htmlspecialchars($event['title']); ?></h1>
                    <div class="flex items-center gap-2 text-muted-foreground mb-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            <?php echo $event['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo ucfirst($event['status']); ?>
                        </span>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($event['category_name']); ?></span>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($event['type_name']); ?></span>
                    </div>
                    <p class="text-muted-foreground whitespace-pre-line"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Event Details -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Event Details</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Date & Time</dt>
                            <dd class="text-sm mt-1"><?php echo date('F d, Y h:i A', strtotime($event['event_date'])); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Location</dt>
                            <dd class="text-sm mt-1"><?php echo htmlspecialchars($event['location']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Price</dt>
                            <dd class="text-sm mt-1">
                                <?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'Free'; ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Available Slots</dt>
                            <dd class="text-sm mt-1"><?php echo $event['slots']; ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Registration Deadline</dt>
                            <dd class="text-sm mt-1"><?php echo date('F d, Y h:i A', strtotime($event['registration_deadline'])); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Action Buttons -->
                <div class="rounded-lg border bg-card p-6">
                    <div class="space-y-3">
                        <a href="edit-event.php?id=<?php echo $event['id']; ?>" 
                           class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            Edit Event
                        </a>
                        <a href="event-registrations.php?id=<?php echo $event['id']; ?>" 
                           class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                            View Registrations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 