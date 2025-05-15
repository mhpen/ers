<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../models/Admin.php';
$adminModel = new Admin($conn);
$stats = $adminModel->getDashboardStats();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Admin Dashboard</h1>
            <p class="text-muted-foreground mt-2">Monitor and manage the event registration system.</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Pending Client Approvals -->
            <div class="rounded-lg border bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="rounded-lg bg-gray-100 p-2">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium">Client Approvals</h2>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold"><?php echo $stats['pending_approvals']; ?></div>
                        <p class="text-xs text-gray-500 mt-1">Pending client registrations</p>
                    </div>
                </div>
            </div>

            <!-- Pending Event Approvals -->
            <div class="rounded-lg border bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="rounded-lg bg-gray-100 p-2">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium">Event Approvals</h2>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold"><?php echo $stats['pending_events']; ?></div>
                        <p class="text-xs text-gray-500 mt-1">Pending event approvals</p>
                    </div>
                </div>
            </div>

            <!-- Total Events -->
            <div class="rounded-lg border bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="rounded-lg bg-gray-100 p-2">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium">Total Events</h2>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold"><?php echo $stats['total_events']; ?></div>
                        <p class="text-xs text-gray-500 mt-1">Events across all clients</p>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="rounded-lg border bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="rounded-lg bg-gray-100 p-2">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium">Active Users</h2>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold"><?php echo $stats['active_users']; ?></div>
                        <p class="text-xs text-gray-500 mt-1">Total active users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events and Clients Section -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Recent Events -->
            <div class="rounded-lg border bg-white shadow-sm">
                <div class="p-4 border-b bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Recent Events</h2>
                        <a href="events.php" class="text-sm text-gray-600 hover:text-gray-900">View All</a>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="overflow-y-auto max-h-[400px] scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent pr-2">
                        <?php if (!empty($stats['recent_events'])): ?>
                            <?php foreach ($stats['recent_events'] as $event): ?>
                                <a href="eventDetails.php?id=<?php echo $event['id']; ?>" 
                                   class="block mb-3 p-3 rounded-lg border bg-white hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($event['title']); ?></p>
                                                <span class="shrink-0 px-2.5 py-0.5 text-xs rounded-full whitespace-nowrap
                                                    <?php echo match($event['status']) {
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'approved' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    }; ?>">
                                                    <?php echo ucfirst($event['status']); ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-sm text-gray-500 truncate">
                                                    <?php echo htmlspecialchars($event['client_name']); ?>
                                                </span>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-sm text-gray-500 whitespace-nowrap">
                                                    <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-6">No recent events</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="rounded-lg border bg-white shadow-sm">
                <div class="p-4 border-b bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Recent Clients</h2>
                        <a href="clients.php" class="text-sm text-gray-600 hover:text-gray-900">View All</a>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="overflow-y-auto max-h-[400px] scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent pr-2">
                        <?php if (!empty($stats['recent_clients'])): ?>
                            <?php foreach ($stats['recent_clients'] as $client): ?>
                                <a href="clientDetails.php?id=<?php echo $client['id']; ?>" 
                                   class="block mb-3 p-3 rounded-lg border bg-white hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($client['name']); ?></p>
                                                <span class="shrink-0 px-2.5 py-0.5 text-xs rounded-full whitespace-nowrap
                                                    <?php echo $client['approved'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                    <?php echo $client['approved'] ? 'Approved' : 'Pending'; ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-sm text-gray-500 truncate">
                                                    <?php echo htmlspecialchars($client['organization']); ?>
                                                </span>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-sm text-gray-500 whitespace-nowrap">
                                                    Joined <?php echo timeAgo($client['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-6">No recent clients</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add custom scrollbar styles -->
    <style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #E5E7EB;
        border-radius: 3px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: #D1D5DB;
    }

    /* For Firefox */
    .scrollbar-thin {
        scrollbar-width: thin;
        scrollbar-color: #E5E7EB transparent;
    }
    </style>
</body>
</html>

<?php
function timeAgo($timestamp) {
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