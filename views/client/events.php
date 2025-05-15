<?php
require_once '../../controllers/client/EventController.php';
$eventController = new EventController();

// Get query parameters
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$view = $_GET['view'] ?? 'table';
$page = max(1, intval($_GET['page'] ?? 1));

// Get paginated events
$result = $eventController->getClientEvents($page);
$events = $result['events'];

// Filter events based on search if needed
if ($search) {
    $events = array_filter($events, function($event) use ($search) {
        return stripos($event['title'], $search) !== false ||
               stripos($event['location'], $search) !== false;
    });
}

// Sort events if needed
usort($events, function($a, $b) use ($sort) {
    return match($sort) {
        'date_asc' => strtotime($a['event_date']) - strtotime($b['event_date']),
        'date_desc' => strtotime($b['event_date']) - strtotime($a['event_date']),
        'title_asc' => strcmp($a['title'], $b['title']),
        'title_desc' => strcmp($b['title'], $a['title']),
        'status' => strcmp($a['status'], $b['status']),
        default => strtotime($b['event_date']) - strtotime($a['event_date'])
    };
});

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <main class="ml-64 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">My Events</h1>
                <p class="text-muted-foreground mt-2">Manage your events and track their performance</p>
            </div>
            <a href="create-event.php" 
               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Event
            </a>
        </div>

        <?php include_once '../shared/alerts.php'; ?>

        <!-- Add this right after the alerts include -->
        <div id="loadingIndicator" class="hidden">
            <?php include __DIR__ . '/components/loading-spinner.php'; ?>
        </div>

        <!-- Wrap the events container in a parent div -->
        <div id="eventsContent">
            <!-- Filters and View Toggle -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" 
                               id="searchInput"
                               placeholder="Search events..."
                               value="<?php echo htmlspecialchars($search); ?>"
                               class="h-10 w-[250px] rounded-md border border-input bg-background pl-8 pr-4 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    </div>

                    <!-- Sort -->
                    <select id="sortSelect" 
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Latest First</option>
                        <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="title_asc" <?php echo $sort === 'title_asc' ? 'selected' : ''; ?>>Title A-Z</option>
                        <option value="title_desc" <?php echo $sort === 'title_desc' ? 'selected' : ''; ?>>Title Z-A</option>
                        <option value="status" <?php echo $sort === 'status' ? 'selected' : ''; ?>>Status</option>
                    </select>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center gap-2 border rounded-lg p-1">
                    <button onclick="setView('table')" 
                            class="inline-flex items-center justify-center rounded-md p-1.5 text-sm <?php echo $view === 'table' ? 'bg-muted' : ''; ?>"
                            title="Table View">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <button onclick="setView('grid')" 
                            class="inline-flex items-center justify-center rounded-md p-1.5 text-sm <?php echo $view === 'grid' ? 'bg-muted' : ''; ?>"
                            title="Grid View">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Events View -->
            <div id="eventsContainer">
                <?php if ($view === 'table'): ?>
                    <!-- Table View -->
                    <?php include 'views/table.php'; ?>
                <?php else: ?>
                    <!-- Grid View -->
                    <?php include 'views/grid.php'; ?>
                <?php endif; ?>

                <!-- Add this before closing the eventsContainer div -->
                <div class="mt-4">
                    <?php 
                    include __DIR__ . '/components/pagination.php';
                    renderPagination($result['totalPages'], $result['currentPage'], 'events.php');
                    ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        const loadingIndicator = document.getElementById('loadingIndicator');
        const eventsContent = document.getElementById('eventsContent');
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        function showLoading() {
            loadingIndicator.classList.remove('hidden');
            eventsContent.classList.add('opacity-50', 'pointer-events-none');
        }

        function hideLoading() {
            loadingIndicator.classList.add('hidden');
            eventsContent.classList.remove('opacity-50', 'pointer-events-none');
        }

        // Search functionality with loading state
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                showLoading();
                updateQueryParams();
            }, 300);
        });

        // Sort functionality with loading state
        document.getElementById('sortSelect').addEventListener('change', function() {
            showLoading();
            updateQueryParams();
        });

        // View toggle functionality with loading state
        function setView(viewType) {
            showLoading();
            const params = new URLSearchParams(window.location.search);
            params.set('view', viewType);
            window.location.search = params.toString();
        }

        // Update URL with filters
        function updateQueryParams() {
            const params = new URLSearchParams(window.location.search);
            params.set('search', searchInput.value);
            params.set('sort', document.getElementById('sortSelect').value);
            window.location.search = params.toString();
        }

        // Add loading state for initial page load
        window.addEventListener('load', function() {
            hideLoading();
        });

        // Add loading state for navigation
        window.addEventListener('beforeunload', function() {
            showLoading();
        });

        // Handle loading state for AJAX requests if you add any
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a:not([target="_blank"])');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.hasAttribute('download')) {
                        showLoading();
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
function getStatusBadgeClasses($status) {
    return match($status) {
        'published' => 'border-transparent bg-green-50 text-green-600',
        'draft' => 'border-transparent bg-yellow-50 text-yellow-600',
        'pending' => 'border-transparent bg-blue-50 text-blue-600',
        'cancelled' => 'border-transparent bg-red-50 text-red-600',
        default => 'border-transparent bg-gray-50 text-gray-600'
    };
}
?> 