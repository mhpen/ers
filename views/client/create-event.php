<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');

require_once '../../controllers/client/EventController.php';
$eventController = new EventController();

// Get categories and event types for dropdowns
$categories = $eventController->getCategories();
$eventTypes = $eventController->getEventTypes();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <div class="flex ml-64">
        <!-- Left Column - Form -->
        <div class="w-2/3 p-8 border-r border-gray-200">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight">Create New Event</h1>
                <p class="text-muted-foreground mt-2">Fill in the details to create your event.</p>
            </div>

            <form action="../../controllers/client/EventController.php" method="POST" enctype="multipart/form-data" class="space-y-8">
                <input type="hidden" name="action" value="create">
                
                <!-- Basic Information -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Basic Information</h2>
                        <p class="text-sm text-muted-foreground mt-1">General details about your event.</p>
                    </div>
                    
                    <div class="grid gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="title">Event Title</label>
                            <input type="text" id="title" name="title" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>

                        <div>
                            <label class="text-sm font-medium leading-none" for="description">Description</label>
                            <textarea id="description" name="description" rows="4" required
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors mt-1.5"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium leading-none" for="category">Category</label>
                                <select id="category" name="category_id" required
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium leading-none" for="type">Event Type</label>
                                <select id="type" name="type_id" required onchange="toggleEventTypeFields()"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                    <option value="">Select Type</option>
                                    <?php foreach ($eventTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>">
                                            <?php echo htmlspecialchars($type['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Banner Upload -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Event Banner</h2>
                        <p class="text-sm text-muted-foreground mt-1">Upload an image to represent your event.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium leading-none" for="banner">Upload Banner Image</label>
                        <div class="mt-1.5">
                            <input type="file" id="banner" name="banner" accept="image/*"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium">
                            <p class="text-xs text-muted-foreground mt-1">Recommended size: 1200x600px. Max size: 2MB</p>
                        </div>
                        <div id="bannerPreview" class="hidden mt-4">
                            <div class="aspect-[21/9] relative bg-muted rounded-lg overflow-hidden">
                                <img id="previewImage" src="" alt="Banner preview" class="object-cover w-full h-full">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Location Information -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Location Details</h2>
                        <p class="text-sm text-muted-foreground mt-1">Where will your event take place?</p>
                    </div>

                    <div class="grid gap-6">
                        <div id="physicalLocationField">
                            <label class="text-sm font-medium leading-none" for="location">Physical Location</label>
                            <input type="text" id="location" name="location" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5"
                                placeholder="Enter venue address">
                        </div>

                        <div id="virtualEventFields" class="hidden">
                            <label class="text-sm font-medium leading-none" for="meeting_link">Meeting Link</label>
                            <input type="url" id="meeting_link" name="meeting_link" 
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5"
                                placeholder="e.g., Zoom or Teams meeting link">
                        </div>
                    </div>
                </section>

                <!-- Date and Time -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Schedule</h2>
                        <p class="text-sm text-muted-foreground mt-1">When will your event take place?</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="event_date">Event Date & Time</label>
                            <input type="datetime-local" id="event_date" name="event_date" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                        <div>
                            <label class="text-sm font-medium leading-none" for="registration_deadline">Registration Deadline</label>
                            <input type="datetime-local" id="registration_deadline" name="registration_deadline" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                    </div>
                </section>

                <!-- Capacity -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Capacity</h2>
                        <p class="text-sm text-muted-foreground mt-1">Set attendance limits for your event.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="slots">Total Available Slots</label>
                            <input type="number" id="slots" name="slots" min="1" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                        <div>
                            <label class="text-sm font-medium leading-none" for="max_per_registration">Max Tickets Per Registration</label>
                            <input type="number" id="max_per_registration" name="max_participants_per_registration" min="1" value="1" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                        </div>
                    </div>
                </section>

                <!-- Pricing and Visibility -->
                <section>
                    <div class="border-b border-gray-200 pb-3 mb-6">
                        <h2 class="text-xl font-semibold">Pricing & Visibility</h2>
                        <p class="text-sm text-muted-foreground mt-1">Set your event's price and who can see it.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium leading-none" for="price">Price</label>
                            <div class="relative mt-1.5">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm text-muted-foreground">$</span>
                                <input type="number" id="price" name="price" min="0" step="0.01" required
                                    class="flex h-9 w-full rounded-md border border-input bg-background pl-8 pr-3 py-1 text-sm shadow-sm transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium leading-none" for="visibility">Visibility</label>
                            <select id="visibility" name="visibility" required
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors mt-1.5">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="invite-only">Invite Only</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Submit Buttons -->
                <section class="border-t border-gray-200 pt-6">
                    <div class="flex justify-end gap-4">
                        <button type="submit" name="status" value="draft" 
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            Save as Draft
                        </button>
                        <button type="submit" name="status" value="pending"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                            Submit for Review
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
                    <div id="previewBanner" class="aspect-[21/9] bg-muted flex items-center justify-center">
                        <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 id="previewTitle" class="text-xl font-semibold mb-2">Event Title</h3>
                        <p id="previewDescription" class="text-muted-foreground mb-4">Event description will appear here...</p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span id="previewDate">Date and time</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span id="previewLocation">Location</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <span id="previewPrice">Price</span>
                            </div>
                        </div>

                        <!-- Additional Preview Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium">Category:</span>
                                    <span id="previewCategory" class="text-sm text-muted-foreground">Not selected</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Event Type:</span>
                                    <span id="previewType" class="text-sm text-muted-foreground">Not selected</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Available Slots:</span>
                                    <span id="previewSlots" class="text-sm text-muted-foreground">0</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium">Registration Deadline:</span>
                                    <span id="previewDeadline" class="text-sm text-muted-foreground">Not set</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Banner preview functionality
        document.getElementById('banner').addEventListener('change', function(e) {
            const preview = document.getElementById('bannerPreview');
            const previewImage = document.getElementById('previewImage');
            const previewBanner = document.getElementById('previewBanner');
            
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Update form preview
                    previewImage.src = e.target.result;
                    preview.classList.remove('hidden');
                    
                    // Update card preview
                    previewBanner.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                }
                
                reader.readAsDataURL(e.target.files[0]);
            } else {
                preview.classList.add('hidden');
                previewImage.src = '';
            }
        });

        // Event type fields toggle
        function toggleEventTypeFields() {
            const eventType = document.getElementById('type').value;
            const virtualFields = document.getElementById('virtualEventFields');
            const physicalLocationField = document.getElementById('physicalLocationField');
            const meetingLinkInput = document.getElementById('meeting_link');
            const locationInput = document.getElementById('location');

            // Show/hide fields based on event type
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
            } else { // Physical or not selected
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
        });

        // Add preview functionality
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

        // Additional preview updates
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
    </script>
</body>
</html>