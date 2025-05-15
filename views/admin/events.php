<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../controllers/admin/EventController.php';
$eventController = new EventController();

// Get current page from query string
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage); // Ensure page is at least 1

// Get events with pagination
$eventsData = $eventController->getAllEvents($currentPage, 10);
$events = $eventsData['events'];
$totalPages = $eventsData['pages'];

$categories = $eventController->getCategories();
$eventTypes = $eventController->getEventTypes();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Events</h1>
                <p class="text-muted-foreground mt-2">Manage and approve events in the system.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-lg border bg-white shadow-sm p-6 mb-6">
            <div class="grid grid-cols-4 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status-filter" class="w-full h-10 rounded-md border bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Category</label>
                    <select id="category-filter" class="w-full h-10 rounded-md border bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Event Type</label>
                    <select id="type-filter" class="w-full h-10 rounded-md border bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="all">All Types</option>
                        <?php foreach ($eventTypes as $eventType): ?>
                            <option value="<?php echo $eventType['id']; ?>">
                                <?php echo htmlspecialchars($eventType['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Sort By</label>
                    <select id="sort-filter" class="w-full h-10 rounded-md border bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="title">Title A-Z</option>
                        <option value="date">Event Date</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Events List -->
        <div class="rounded-lg border bg-white shadow-sm">
            <div class="p-6">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 rounded-lg">
                            <tr>
                                <th class="px-6 py-4 font-medium text-gray-500">Event</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Organizer</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Date</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Category</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 py-4 font-medium text-gray-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="events-table-body" class="divide-y">
                            <?php foreach ($events as $event): ?>
                                <tr class="hover:bg-gray-50" 
                                    data-status="<?php echo $event['status']; ?>"
                                    data-category="<?php echo $event['category_id']; ?>"
                                    data-type="<?php echo $event['type_id']; ?>"
                                    data-date="<?php echo $event['event_date']; ?>"
                                    data-title="<?php echo htmlspecialchars($event['title']); ?>">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <?php if ($event['banner']): ?>
                                                <img src="../../public/<?php echo htmlspecialchars($event['banner']); ?>" 
                                                     alt="Event banner" 
                                                     class="h-10 w-10 rounded-lg object-cover">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="font-medium"><?php echo htmlspecialchars($event['title']); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($event['type_name']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($event['client_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($event['category_name']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo match($event['status']) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }; ?>">
                                            <?php echo ucfirst($event['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="eventDetails.php?id=<?php echo $event['id']; ?>" 
                                               class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-gray-200 hover:bg-gray-100 h-9 px-4">
                                                View Details
                                            </a>
                                            
                                            <?php if ($event['status'] === 'pending'): ?>
                                                <button onclick="approveEvent(<?php echo $event['id']; ?>)"
                                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-green-600 text-white hover:bg-green-700 h-9 px-4">
                                                    Approve
                                                </button>
                                                <button onclick="rejectEvent(<?php echo $event['id']; ?>)"
                                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-red-600 text-white hover:bg-red-700 h-9 px-4">
                                                    Reject
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6 mt-4">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?page=<?php echo $currentPage - 1; ?>" 
                                       class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?page=<?php echo $currentPage + 1; ?>" 
                                       class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing page <span class="font-medium"><?php echo $currentPage; ?></span> of
                                        <span class="font-medium"><?php echo $totalPages; ?></span>
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                        <?php if ($currentPage > 1): ?>
                                            <a href="?page=<?php echo $currentPage - 1; ?>" 
                                               class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php
                                        // Show page numbers
                                        for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++):
                                        ?>
                                            <a href="?page=<?php echo $i; ?>" 
                                               class="relative inline-flex items-center px-4 py-2 text-sm font-semibold <?php echo $i === $currentPage ? 'bg-gray-900 text-white focus-visible:outline-2' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>

                                        <?php if ($currentPage < $totalPages): ?>
                                            <a href="?page=<?php echo $currentPage + 1; ?>" 
                                               class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Get all filter elements
        const statusFilter = document.getElementById('status-filter');
        const categoryFilter = document.getElementById('category-filter');
        const typeFilter = document.getElementById('type-filter');
        const sortFilter = document.getElementById('sort-filter');
        const eventsTableBody = document.getElementById('events-table-body');

        // Add event listeners to all filters
        [statusFilter, categoryFilter, typeFilter, sortFilter].forEach(filter => {
            filter.addEventListener('change', filterEvents);
        });

        // Event approval and rejection functions
        function approveEvent(eventId) {
            if (confirm('Are you sure you want to approve this event?')) {
                submitEventAction(eventId, 'approve');
            }
        }

        function rejectEvent(eventId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason !== null && reason.trim() !== '') {
                submitEventAction(eventId, 'reject', reason);
            } else if (reason !== null) {
                alert('Please provide a reason for rejection');
            }
        }

        function submitEventAction(eventId, action, reason = '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../controllers/admin/EventController.php';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            const eventIdInput = document.createElement('input');
            eventIdInput.type = 'hidden';
            eventIdInput.name = 'event_id';
            eventIdInput.value = eventId;
            form.appendChild(eventIdInput);

            if (reason) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);
            }

            document.body.appendChild(form);
            form.submit();
        }

        function filterEvents() {
            const rows = Array.from(eventsTableBody.getElementsByTagName('tr'));
            const status = statusFilter.value;
            const category = categoryFilter.value;
            const type = typeFilter.value;
            const sort = sortFilter.value;

            // Filter rows
            let filteredRows = rows.filter(row => {
                if (!row.dataset.status) return false; // Skip rows without data attributes
                
                const rowStatus = row.dataset.status;
                const rowCategory = row.dataset.category;
                const rowType = row.dataset.type;

                return (status === 'all' || rowStatus === status) &&
                       (category === 'all' || rowCategory === category) &&
                       (type === 'all' || rowType === type);
            });

            // Sort rows
            filteredRows.sort((a, b) => {
                switch(sort) {
                    case 'newest':
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    case 'oldest':
                        return new Date(a.dataset.date) - new Date(b.dataset.date);
                    case 'title':
                        return a.dataset.title.localeCompare(b.dataset.title);
                    case 'date':
                        return new Date(a.dataset.date) - new Date(b.dataset.date);
                    default:
                        return 0;
                }
            });

            // Clear and repopulate table
            eventsTableBody.innerHTML = '';
            
            if (filteredRows.length === 0) {
                eventsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No events found matching the selected filters
                        </td>
                    </tr>
                `;
            } else {
                filteredRows.forEach(row => {
                    // Clone the row to preserve event listeners
                    const clonedRow = row.cloneNode(true);
                    
                    // Re-attach event listeners to approve/reject buttons
                    const approveBtn = clonedRow.querySelector('button[onclick^="approveEvent"]');
                    const rejectBtn = clonedRow.querySelector('button[onclick^="rejectEvent"]');
                    
                    if (approveBtn) {
                        const eventId = approveBtn.getAttribute('onclick').match(/\d+/)[0];
                        approveBtn.onclick = () => approveEvent(eventId);
                    }
                    
                    if (rejectBtn) {
                        const eventId = rejectBtn.getAttribute('onclick').match(/\d+/)[0];
                        rejectBtn.onclick = () => rejectEvent(eventId);
                    }
                    
                    eventsTableBody.appendChild(clonedRow);
                });
            }
        }

        // Initial filter
        filterEvents();
    </script>
</body>
</html> 