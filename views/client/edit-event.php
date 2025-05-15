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
$categories = $eventController->getCategories();
$eventTypes = $eventController->getEventTypes();

// Decode location if it's JSON (for virtual/hybrid events)
$location = $event['location'];
$virtualLink = null;
if (in_array($event['type_id'], ['2', '3'])) { // Virtual or Hybrid
    $locationData = json_decode($location, true);
    $location = $locationData['physical'] ?? '';
    $virtualLink = $locationData['virtual'] ?? '';
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <div class="flex ml-64">
        <!-- Left Column - Form -->
        <div class="w-2/3 p-8 border-r border-gray-200">
        <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight">Edit Event</h1>
            <p class="text-muted-foreground mt-2">Update your event details.</p>
        </div>

            <form action="../../controllers/client/EventController.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
            
                <!-- Banner Section -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Event Banner</h2>
                        <p class="text-sm text-muted-foreground mt-1">Update your event's banner image.</p>
                    </div>

                <?php if ($event['banner']): ?>
                        <div class="mb-6">
                            <h3 class="text-sm font-medium mb-2">Current Banner</h3>
                        <div class="aspect-[21/9] relative bg-muted rounded-lg overflow-hidden">
                            <img 
                                src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                alt="Current banner"
                                class="object-cover w-full h-full"
                            >
                            <input type="hidden" name="current_banner" value="<?php echo htmlspecialchars($event['banner']); ?>">
                            
                            <button type="submit" name="action" value="delete_banner" 
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors"
                                onclick="return confirm('Are you sure you want to delete the banner?')">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                    <div>
                        <label class="text-sm font-medium leading-none" for="banner">Upload New Banner</label>
                        <div class="mt-1.5">
                            <input type="file" id="banner" name="banner" accept="image/*"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium">
                            <p class="text-xs text-muted-foreground mt-1">Recommended size: 1200x600px. Max size: 2MB</p>
                        </div>
                    </div>
                </section>

                <!-- Basic Information -->
                <section>
                    <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                    <div class="grid gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="title">Event Title</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo htmlspecialchars($event['title']); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>

                        <div>
                            <label class="text-sm font-medium leading-none" for="description">Description</label>
                            <textarea id="description" name="description" rows="4" required
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors mt-1.5"><?php echo htmlspecialchars($event['description']); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium leading-none" for="category">Category</label>
                                <select id="category" name="category_id" required
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $category['id'] == $event['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium leading-none" for="type">Event Type</label>
                                <select id="type" name="type_id" required onchange="toggleEventTypeFields()"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                    <?php foreach ($eventTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"
                                            <?php echo $type['id'] == $event['type_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Location Information -->
                <section>
                    <h2 class="text-xl font-semibold mb-4">Location Details</h2>
                    <div class="grid gap-6">
                        <div id="physicalLocationField">
                            <label class="text-sm font-medium leading-none" for="location">Physical Location</label>
                            <input type="text" id="location" name="location"
                                value="<?php echo htmlspecialchars($location); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>

                        <div id="virtualEventFields" class="<?php echo !in_array($event['type_id'], ['2', '3']) ? 'hidden' : ''; ?>">
                            <label class="text-sm font-medium leading-none" for="meeting_link">Meeting Link</label>
                            <input type="url" id="meeting_link" name="meeting_link"
                                value="<?php echo htmlspecialchars($virtualLink); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                    </div>
                </section>

                <!-- Date and Time -->
                <section>
                    <h2 class="text-xl font-semibold mb-4">Schedule</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="event_date">Event Date & Time</label>
                            <input type="datetime-local" id="event_date" name="event_date" required
                                value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                        <div>
                            <label class="text-sm font-medium leading-none" for="registration_deadline">Registration Deadline</label>
                            <input type="datetime-local" id="registration_deadline" name="registration_deadline" required
                                value="<?php echo date('Y-m-d\TH:i', strtotime($event['registration_deadline'])); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                    </div>
                </section>

                <!-- Capacity -->
                <section>
                    <h2 class="text-xl font-semibold mb-4">Capacity</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="slots">Total Available Slots</label>
                            <input type="number" id="slots" name="slots" min="1" required
                                value="<?php echo htmlspecialchars($event['slots']); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                        <div>
                            <label class="text-sm font-medium leading-none" for="max_per_registration">Max Tickets Per Registration</label>
                            <input type="number" id="max_per_registration" name="max_participants_per_registration" min="1" required
                                value="<?php echo htmlspecialchars($event['max_participants_per_registration']); ?>"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                    </div>
                </section>

                <!-- Pricing and Visibility -->
                <section>
                    <h2 class="text-xl font-semibold mb-4">Pricing & Visibility</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="price">Price</label>
                            <div class="relative mt-1.5">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm text-muted-foreground">$</span>
                                <input type="number" id="price" name="price" min="0" step="0.01" required
                                    value="<?php echo htmlspecialchars($event['price']); ?>"
                                    class="flex h-9 w-full rounded-md border border-input bg-background pl-8 pr-3 py-1 text-sm shadow-sm transition-colors">
                    </div>
                </div>

                        <div>
                            <label class="text-sm font-medium leading-none" for="visibility">Visibility</label>
                            <select id="visibility" name="visibility" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                <option value="public" <?php echo $event['visibility'] === 'public' ? 'selected' : ''; ?>>Public</option>
                                <option value="private" <?php echo $event['visibility'] === 'private' ? 'selected' : ''; ?>>Private</option>
                                <option value="invite-only" <?php echo $event['visibility'] === 'invite-only' ? 'selected' : ''; ?>>Invite Only</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Submit Button -->
                <section class="border-t border-gray-200 pt-6">
                    <div class="flex justify-end gap-4">
                        <button type="submit"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Update Event
                    </button>
                    </div>
                </section>
            </form>
        </div>

        <!-- Right Column - Preview -->
        <div class="w-1/3 p-8">
            <div class="sticky top-8">
                <h2 class="text-lg font-semibold mb-4">Event Preview</h2>
                <div class="rounded-lg border bg-card overflow-hidden">
                    <div id="previewBanner" class="aspect-[21/9] bg-muted">
                        <?php if ($event['banner']): ?>
                            <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                 alt="Event banner" 
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="h-full flex items-center justify-center">
                                <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 id="previewTitle" class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p id="previewDescription" class="text-muted-foreground mb-4"><?php echo htmlspecialchars($event['description']); ?></p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span id="previewDate"><?php echo date('F j, Y g:i A', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span id="previewLocation"><?php echo htmlspecialchars($location); ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <span id="previewPrice"><?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'Free'; ?></span>
                            </div>
                        </div>

                        <!-- Additional Preview Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium">Category:</span>
                                    <span id="previewCategory" class="text-sm text-muted-foreground"><?php echo htmlspecialchars($event['category_name']); ?></span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Event Type:</span>
                                    <span id="previewType" class="text-sm text-muted-foreground"><?php echo htmlspecialchars($event['type_name']); ?></span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Available Slots:</span>
                                    <span id="previewSlots" class="text-sm text-muted-foreground"><?php echo htmlspecialchars($event['slots']); ?></span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Registration Deadline:</span>
                                    <span id="previewDeadline" class="text-sm text-muted-foreground"><?php echo date('F j, Y g:i A', strtotime($event['registration_deadline'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle virtual event fields based on event type
        function toggleEventTypeFields() {
            const eventType = document.getElementById('type').value;
            const virtualFields = document.getElementById('virtualEventFields');
            const physicalLocationField = document.getElementById('physicalLocationField');
            const meetingLinkInput = document.getElementById('meeting_link');
            const locationInput = document.getElementById('location');

            if (eventType === '2') { // Virtual
                virtualFields.classList.remove('hidden');
                physicalLocationField.classList.add('hidden');
                meetingLinkInput.required = true;
                locationInput.required = false;
            } else if (eventType === '3') { // Hybrid
                virtualFields.classList.remove('hidden');
                physicalLocationField.classList.remove('hidden');
                meetingLinkInput.required = true;
                locationInput.required = true;
            } else { // Physical
                virtualFields.classList.add('hidden');
                physicalLocationField.classList.remove('hidden');
                meetingLinkInput.required = false;
                locationInput.required = true;
            }
        }

        // Initialize date validation
        document.addEventListener('DOMContentLoaded', function() {
            const eventDateInput = document.getElementById('event_date');
            const registrationDeadlineInput = document.getElementById('registration_deadline');

            // Set minimum date to today
            const today = new Date();
            const todayStr = today.toISOString().slice(0, 16);
            eventDateInput.min = todayStr;
            registrationDeadlineInput.min = todayStr;

            // Ensure registration deadline is before event date
            eventDateInput.addEventListener('change', function() {
                registrationDeadlineInput.max = this.value;
            });

            registrationDeadlineInput.addEventListener('change', function() {
                if (this.value > eventDateInput.value) {
                    alert('Registration deadline must be before the event date');
                    this.value = eventDateInput.value;
                }
            });

            // Initialize event type fields
            toggleEventTypeFields();
        });

        // Live preview updates
        document.getElementById('title').addEventListener('input', function(e) {
            document.getElementById('previewTitle').textContent = e.target.value || 'Event Title';
        });

        document.getElementById('description').addEventListener('input', function(e) {
            document.getElementById('previewDescription').textContent = e.target.value || 'Event description will appear here...';
        });

        document.getElementById('event_date').addEventListener('input', function(e) {
            const date = new Date(e.target.value);
            document.getElementById('previewDate').textContent = date.toLocaleString();
        });

        document.getElementById('location').addEventListener('input', function(e) {
            document.getElementById('previewLocation').textContent = e.target.value || 'Location';
        });

        document.getElementById('price').addEventListener('input', function(e) {
            const price = parseFloat(e.target.value);
            document.getElementById('previewPrice').textContent = price ? `$${price.toFixed(2)}` : 'Free';
        });

        document.getElementById('category').addEventListener('change', function(e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            document.getElementById('previewCategory').textContent = selectedOption.text || 'Not selected';
        });

        document.getElementById('type').addEventListener('change', function(e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            document.getElementById('previewType').textContent = selectedOption.text || 'Not selected';
        });

        document.getElementById('slots').addEventListener('input', function(e) {
            document.getElementById('previewSlots').textContent = e.target.value || '0';
        });

        document.getElementById('registration_deadline').addEventListener('input', function(e) {
            const date = new Date(e.target.value);
            document.getElementById('previewDeadline').textContent = date.toLocaleString() || 'Not set';
        });

        // Update banner preview when a new file is selected
        document.getElementById('banner').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBanner = document.getElementById('previewBanner');
                    previewBanner.innerHTML = `<img src="${e.target.result}" alt="Banner preview" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html> 