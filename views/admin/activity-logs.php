<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('admin');

require_once '../../controllers/admin/ActivityLogController.php';
$controller = new ActivityLogController();

// Get filter parameters
$type = $_GET['type'] ?? null;
$date = $_GET['date'] ?? null;
$sort = $_GET['sort'] ?? 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get activity logs with pagination
$result = $controller->getFilteredLogs($type, $date, $sort, $page);
$logs = $result['logs'];
$pagination = $result['pagination'];
$activityTypes = $controller->getActivityTypes();

// If it's an AJAX request, only return the logs content
if (isset($_GET['ajax'])) {
    if (empty($logs)): ?>
        <p class="text-center text-muted-foreground py-8">No activity logs found.</p>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <div class="flex items-start gap-4 border-b pb-6">
                <div class="h-8 w-8 rounded-full <?php echo getActivityIconClass($log['action']); ?> flex items-center justify-center">
                    <?php echo getActivityIcon($log['action']); ?>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="font-medium"><?php echo formatActivityAction($log['action']); ?></p>
                        <span class="text-sm text-muted-foreground">
                            <?php echo formatTimeAgo($log['created_at']); ?>
                        </span>
                    </div>
                    <p class="text-sm text-muted-foreground mt-1">
                        <?php echo htmlspecialchars($log['description']); ?>
                    </p>
                    <div class="mt-2 text-xs">
                        <span class="text-muted-foreground">
                            By: <?php echo htmlspecialchars($log['actor_name']); ?> 
                            (<?php echo ucfirst($log['actor_type']); ?>)
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif;
    exit;
}

// Regular page load continues here...
include_once '../shared/header.php';
?>

<head>
    <style>
    .loading {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 24px;
        height: 24px;
        margin: -12px 0 0 -12px;
        border: 2px solid transparent;
        border-top-color: #000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
</head>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Activity Logs</h1>
            <p class="text-muted-foreground mt-2">Track all system activities and changes.</p>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex gap-4">
            <form class="flex gap-4" method="GET" id="filterForm">
                <select name="type" class="rounded-md border px-3 py-2 text-sm">
                    <option value="">All Activities</option>
                    <?php foreach ($activityTypes as $activityType): ?>
                        <option value="<?php echo htmlspecialchars($activityType); ?>"
                                <?php echo $type === $activityType ? 'selected' : ''; ?>>
                            <?php echo ucfirst($activityType); ?> Activities
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="date" 
                       value="<?php echo $date; ?>"
                       class="rounded-md border px-3 py-2 text-sm">

                <select name="sort" class="rounded-md border px-3 py-2 text-sm">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                </select>

                <a href="activity-logs.php" class="text-sm text-muted-foreground hover:text-primary py-2">Reset</a>
            </form>
        </div>

        <!-- Activity Log List -->
        <div class="rounded-lg border bg-card">
            <div class="p-6">
                <div class="space-y-6">
                    <?php if (empty($logs)): ?>
                        <p class="text-center text-muted-foreground py-8">No activity logs found.</p>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="flex items-start gap-4 border-b pb-6">
                                <div class="h-8 w-8 rounded-full <?php echo getActivityIconClass($log['action']); ?> flex items-center justify-center">
                                    <?php echo getActivityIcon($log['action']); ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium"><?php echo formatActivityAction($log['action']); ?></p>
                                        <span class="text-sm text-muted-foreground">
                                            <?php echo formatTimeAgo($log['created_at']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-muted-foreground mt-1">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </p>
                                    <div class="mt-2 text-xs">
                                        <span class="text-muted-foreground">
                                            By: <?php echo htmlspecialchars($log['actor_name']); ?> 
                                            (<?php echo ucfirst($log['actor_type']); ?>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['totalPages'] > 1): ?>
            <div class="mt-6 flex justify-center gap-2">
                <?php if ($pagination['currentPage'] > 1): ?>
                    <a href="?page=<?php echo $pagination['currentPage'] - 1; ?><?php echo $type ? "&type=$type" : ''; ?><?php echo $date ? "&date=$date" : ''; ?><?php echo $sort ? "&sort=$sort" : ''; ?>" 
                       class="px-3 py-1 rounded-md border hover:bg-gray-100">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $type ? "&type=$type" : ''; ?><?php echo $date ? "&date=$date" : ''; ?><?php echo $sort ? "&sort=$sort" : ''; ?>" 
                       class="px-3 py-1 rounded-md border <?php echo $i === $pagination['currentPage'] ? 'bg-primary text-white' : 'hover:bg-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                    <a href="?page=<?php echo $pagination['currentPage'] + 1; ?><?php echo $type ? "&type=$type" : ''; ?><?php echo $date ? "&date=$date" : ''; ?><?php echo $sort ? "&sort=$sort" : ''; ?>" 
                       class="px-3 py-1 rounded-md border hover:bg-gray-100">
                        Next
                    </a>
                <?php endif; ?>
            </div>

            <div class="mt-2 text-center text-sm text-muted-foreground">
                Showing <?php echo ($pagination['currentPage'] - 1) * $pagination['itemsPerPage'] + 1; ?> 
                to <?php echo min($pagination['currentPage'] * $pagination['itemsPerPage'], $pagination['totalRecords']); ?> 
                of <?php echo $pagination['totalRecords']; ?> records
            </div>
        <?php endif; ?>
    </main>

    <?php
    // Helper functions
    function getActivityIconClass($action) {
        return match (explode('_', $action)[0]) {
            'create' => 'bg-green-100',
            'update' => 'bg-blue-100',
            'delete' => 'bg-red-100',
            'approve' => 'bg-green-100',
            'reject' => 'bg-red-100',
            default => 'bg-primary/10'
        };
    }

    function getActivityIcon($action) {
        $iconColor = match (explode('_', $action)[0]) {
            'create' => 'text-green-600',
            'update' => 'text-blue-600',
            'delete' => 'text-red-600',
            'approve' => 'text-green-600',
            'reject' => 'text-red-600',
            default => 'text-primary'
        };

        return match (explode('_', $action)[0]) {
            'create' => "<svg class='h-4 w-4 {$iconColor}' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4'/>
                        </svg>",
            'update' => "<svg class='h-4 w-4 {$iconColor}' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'/>
                        </svg>",
            'delete' => "<svg class='h-4 w-4 {$iconColor}' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                        </svg>",
            default => "<svg class='h-4 w-4 {$iconColor}' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/>
                        </svg>"
        };
    }

    function formatActivityAction($action) {
        return ucwords(str_replace('_', ' ', $action));
    }

    function formatTimeAgo($timestamp) {
        $datetime = new DateTime($timestamp);
        $now = new DateTime();
        $interval = $now->diff($datetime);

        if ($interval->y > 0) return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        if ($interval->m > 0) return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        if ($interval->d > 0) return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        if ($interval->h > 0) return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        if ($interval->i > 0) return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        return 'just now';
    }
    ?>

    <script>
    function fetchLogs() {
        const logsContainer = document.querySelector('.space-y-6');
        logsContainer.classList.add('loading');

        const type = document.querySelector('select[name="type"]').value;
        const date = document.querySelector('input[name="date"]').value;
        const sort = document.querySelector('select[name="sort"]').value;
        const currentPage = new URLSearchParams(window.location.search).get('page') || 1;

        const params = new URLSearchParams({
            type,
            date,
            sort,
            page: currentPage,
            ajax: true
        });

        fetch(`activity-logs.php?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                logsContainer.innerHTML = html;
                // Update URL without reloading the page
                const newUrl = `activity-logs.php?${new URLSearchParams({type, date, sort, page: currentPage}).toString()}`;
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error fetching logs:', error);
                logsContainer.innerHTML = '<p class="text-center text-red-600 py-8">Error loading activity logs</p>';
            })
            .finally(() => {
                logsContainer.classList.remove('loading');
            });
    }

    // Add event listeners to form inputs
    document.querySelector('select[name="type"]').addEventListener('change', fetchLogs);
    document.querySelector('input[name="date"]').addEventListener('change', fetchLogs);
    document.querySelector('select[name="sort"]').addEventListener('change', fetchLogs);

    // Auto-refresh every 30 seconds
    setInterval(fetchLogs, 30000);

    // Initial load
    document.addEventListener('DOMContentLoaded', fetchLogs);
    </script>
</body>
</html> 