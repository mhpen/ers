<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');

require_once '../../models/Event.php';
require_once '../../config/config.php';

class ParticipantPageController {
    private $conn;
    private $eventModel;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->eventModel = new Event($conn);
    }

    public function getAvailableEvents() {
        try {
            $sql = "SELECT e.*, c.name as category_name, t.name as type_name,
                    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_participants,
                    JSON_UNQUOTE(JSON_EXTRACT(e.location, '$.physical')) as physical_location,
                    JSON_UNQUOTE(JSON_EXTRACT(e.location, '$.virtual')) as virtual_location
                    FROM events e
                    LEFT JOIN categories c ON e.category_id = c.id
                    LEFT JOIN event_types t ON e.type_id = t.id
                    WHERE e.status = 'published' 
                    AND (e.visibility = 'public' OR e.visibility = 'invite-only')
                    AND e.registration_deadline >= CURRENT_TIMESTAMP
                    AND e.event_date >= CURRENT_DATE
                    HAVING (registered_participants < e.slots OR registered_participants IS NULL)
                    ORDER BY e.event_date ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching available events: " . $e->getMessage());
            return [];
        }
    }

    public function getCategories() {
        try {
            $sql = "SELECT id, name FROM categories ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public function getEventTypes() {
        try {
            $sql = "SELECT id, name FROM event_types ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event types: " . $e->getMessage());
            return [];
        }
    }
}

$controller = new ParticipantPageController();
$events = $controller->getAvailableEvents();
$categories = $controller->getCategories();
$eventTypes = $controller->getEventTypes();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Discover Events</h1>
            <p class="mt-2 text-lg text-gray-600">Find and join upcoming events that interest you</p>
        </div>

        <!-- Search and Filters Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-8">
            <div class="grid gap-4 md:flex md:items-center md:justify-between max-w-full">
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search events..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Filters -->
                <div class="flex gap-4">
                    <select id="categoryFilter" 
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="typeFilter" 
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Types</option>
                        <?php foreach ($eventTypes as $type): ?>
                            <option value="<?php echo $type['id']; ?>">
                                <?php echo htmlspecialchars($type['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php if (empty($events)): ?>
                <div class="col-span-full flex flex-col items-center justify-center py-12 bg-white rounded-lg border border-gray-200">
                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                    </svg>
                    <p class="text-gray-600 text-lg mb-2">No events found</p>
                    <p class="text-gray-500 text-sm">Check back later for new events</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="group bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 event-card" 
                         data-category="<?php echo $event['category_id']; ?>"
                         data-type="<?php echo $event['type_id']; ?>">
                        <!-- Event Banner -->
                        <div class="relative aspect-video">
                            <?php if ($event['banner']): ?>
                                <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <!-- Badges -->
                            <div class="absolute top-2 left-2 flex flex-wrap gap-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-white/90 text-gray-800 shadow-sm">
                                    <?php echo htmlspecialchars($event['category_name']); ?>
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-white/90 
                                      <?php echo $event['price'] > 0 ? 'text-emerald-800' : 'text-gray-800'; ?>">
                                    <?php echo $event['price'] > 0 ? '₱' . number_format($event['price'], 2) : 'Free'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- Event Title and Type -->
                            <div class="mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2 mb-2">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                           <?php echo $event['type_name'] === 'Virtual' ? 'bg-blue-100 text-blue-800' : 
                                                 ($event['type_name'] === 'Hybrid' ? 'bg-purple-100 text-purple-800' : 
                                                  'bg-green-100 text-green-800'); ?>">
                                    <?php echo htmlspecialchars($event['type_name']); ?>
                                </span>
                            </div>

                            <!-- Event Details -->
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                </div>

                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <?php 
                                    if ($event['type_name'] === 'Virtual') {
                                        echo 'Online Event';
                                    } else {
                                        echo htmlspecialchars($event['physical_location']);
                                    }
                                    ?>
                                </div>

                                <?php 
                                $availableSlots = $event['slots'] - ($event['registered_participants'] ?? 0);
                                $slotsClass = $availableSlots <= 5 ? 'text-red-600' : 'text-gray-600';
                                ?>
                                <div class="flex items-center <?php echo $slotsClass; ?>">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span><?php echo "{$availableSlots} slots available"; ?></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-4">
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" 
                                   class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    View Details
                                </a>
                                <?php if ($availableSlots > 0): ?>
                                    <a href="register-event.php?event_id=<?php echo $event['id']; ?>" 
                                       class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors text-center">
                                        Register Now
                                    </a>
                                <?php else: ?>
                                    <button disabled 
                                            class="flex-1 px-4 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                        Event Full
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Registration Modal -->
        <div id="registrationModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Event Registration</h3>
                    <button onclick="closeRegistrationModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div id="eventDetails" class="mb-6">
                    <!-- Event details will be populated by JavaScript -->
                </div>

                <form id="registrationForm" action="../../controllers/participant/RegistrationController.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="event_id" id="eventId">

                    <!-- Add participant details section -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Number
                            </label>
                            <input type="tel" 
                                   name="contact_number" 
                                   required
                                   pattern="[0-9]{11}"
                                   placeholder="09XXXXXXXXX"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Emergency Contact
                            </label>
                            <input type="text" 
                                   name="emergency_contact" 
                                   required
                                   placeholder="Contact Person Name"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm mb-2">
                            <input type="tel" 
                                   name="emergency_number" 
                                   required
                                   pattern="[0-9]{11}"
                                   placeholder="Emergency Contact Number"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Special Requirements or Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Special Requirements/Notes (Optional)
                            </label>
                            <textarea name="notes" 
                                      rows="2" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                      placeholder="Any special requirements or notes"></textarea>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600">
                            <h4 class="font-medium text-gray-900 mb-2">Terms and Conditions</h4>
                            <ul class="list-disc list-inside space-y-2">
                                <li>Registration is non-transferable</li>
                                <li>Payment must be completed within 24 hours</li>
                                <li>Cancellation policy applies</li>
                                <li>You agree to follow event guidelines</li>
                            </ul>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="terms" class="text-sm text-gray-600">
                                    I agree to the terms and conditions
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message Display -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" 
                                onclick="closeRegistrationModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    const eventCards = document.querySelectorAll('.event-card');

    function filterEvents() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedType = typeFilter.value;

        let visibleCount = 0;

        eventCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('.text-muted-foreground')?.textContent.toLowerCase() || '';
            const category = card.dataset.category;
            const type = card.dataset.type;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesType = !selectedType || type === selectedType;

            const shouldShow = matchesSearch && matchesCategory && matchesType;
            card.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) visibleCount++;
        });

        // Show/hide no results message
        const noResultsMessage = document.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!noResultsMessage) {
                const message = document.createElement('div');
                message.className = 'no-results col-span-full text-center py-8';
                message.innerHTML = '<p class="text-muted-foreground">No events match your filters</p>';
                document.querySelector('.grid').appendChild(message);
            }
        } else {
            noResultsMessage?.remove();
        }
    }

    // Debounce function to limit how often the filter runs
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Add event listeners with debounce
    const debouncedFilter = debounce(filterEvents, 300);
    searchInput.addEventListener('input', debouncedFilter);
    categoryFilter.addEventListener('change', filterEvents);
    typeFilter.addEventListener('change', filterEvents);

    // Initial filter
    filterEvents();

    function openRegistrationModal(eventData) {
        const modal = document.getElementById('registrationModal');
        const eventDetails = document.getElementById('eventDetails');
        const eventId = document.getElementById('eventId');

        // Populate event details
        eventDetails.innerHTML = `
            <div class="space-y-3">
                <h4 class="font-medium text-gray-900">${eventData.title}</h4>
                <div class="text-sm text-gray-600 space-y-2">
                    <p><span class="font-medium">Date:</span> ${eventData.date}</p>
                    <p><span class="font-medium">Type:</span> ${eventData.type}</p>
                    <p><span class="font-medium">Available Slots:</span> ${eventData.slots}</p>
                    <p><span class="font-medium">Price:</span> ${eventData.price > 0 ? '₱' + eventData.price.toFixed(2) : 'Free'}</p>
                </div>
            </div>
        `;

        // Set event ID
        eventId.value = eventData.id;

        // Show modal
        modal.classList.remove('hidden');
    }

    function closeRegistrationModal() {
        const modal = document.getElementById('registrationModal');
        modal.classList.add('hidden');
        // Reset form
        document.getElementById('registrationForm').reset();
    }

    // Close modal when clicking outside
    document.getElementById('registrationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRegistrationModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRegistrationModal();
        }
    });
    </script>
</body>
</html>
