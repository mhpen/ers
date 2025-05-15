<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../controllers/admin/EventController.php';
$eventController = new EventController();

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    header('Location: events.php?error=Event ID not provided');
    exit();
}

$event = $eventController->getEventDetails($eventId);
if (!$event) {
    header('Location: events.php?error=Event not found');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <main class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="events.php" class="text-sm text-gray-500 hover:text-gray-900">Events</a>
                    <span class="text-gray-500">/</span>
                    <span class="text-sm">Details</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight"><?php echo htmlspecialchars($event['title']); ?></h1>
                <p class="text-gray-500 mt-2">Event Details and Management</p>
            </div>
            <div class="flex gap-3">
                <?php if ($event['status'] === 'pending'): ?>
                    <button onclick="approveEvent(<?php echo $event['id']; ?>)" 
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Event
                    </button>
                    <button onclick="rejectEvent(<?php echo $event['id']; ?>)" 
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject Event
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Event Details -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="col-span-2 space-y-6">
                <!-- Banner -->
                <?php if ($event['banner']): ?>
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                        <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                             alt="Event banner" 
                             class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="rounded-lg border bg-white shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Description</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-600 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="rounded-lg border bg-white shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Event Details</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Category</label>
                            <p class="font-medium"><?php echo htmlspecialchars($event['category_name']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Event Type</label>
                            <p class="font-medium"><?php echo htmlspecialchars($event['type_name']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Date & Time</label>
                            <p class="font-medium"><?php echo date('F j, Y g:i A', strtotime($event['event_date'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Registration Deadline</label>
                            <p class="font-medium"><?php echo date('F j, Y g:i A', strtotime($event['registration_deadline'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Location</label>
                            <p class="font-medium"><?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Price</label>
                            <p class="font-medium"><?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'Free'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="rounded-lg border bg-white shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Status Information</h2>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Current Status</label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php echo match($event['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    }; ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Created By</label>
                            <p class="font-medium"><?php echo htmlspecialchars($event['client_name']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Created At</label>
                            <p class="font-medium"><?php echo date('F j, Y g:i A', strtotime($event['created_at'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Last Updated</label>
                            <p class="font-medium"><?php echo date('F j, Y g:i A', strtotime($event['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Capacity Information -->
                <div class="rounded-lg border bg-white shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Capacity Information</h2>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Total Slots</label>
                            <p class="font-medium"><?php echo $event['slots']; ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Max Participants per Registration</label>
                            <p class="font-medium"><?php echo $event['max_participants_per_registration']; ?></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm text-gray-500">Visibility</label>
                            <p class="font-medium"><?php echo ucfirst($event['visibility']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function approveEvent(eventId) {
            if (confirm('Are you sure you want to approve this event?')) {
                submitEventAction(eventId, 'approve');
            }
        }

        function rejectEvent(eventId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason !== null && reason.trim() !== '') {
                console.log('Submitting rejection with:', {
                    eventId,
                    reason
                });
                submitEventAction(eventId, 'reject', reason);
            } else {
                alert('Please provide a reason for rejection');
            }
        }

        function submitEventAction(eventId, action, reason = '') {
            console.log('Creating form with:', {
                eventId,
                action,
                reason
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../controllers/admin/EventController.php';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;

            const eventIdInput = document.createElement('input');
            eventIdInput.type = 'hidden';
            eventIdInput.name = 'event_id';
            eventIdInput.value = eventId;

            form.appendChild(actionInput);
            form.appendChild(eventIdInput);

            if (reason) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);
            }

            // Debug output
            console.log('Form inputs:', {
                action: actionInput.value,
                eventId: eventIdInput.value,
                reason: reason
            });

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html> 