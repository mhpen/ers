<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('client');

// Get client ID from session
$client_id = $_SESSION['client']['id'];

try {
    // Get total events count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total_events,
        SUM(CASE WHEN event_date >= CURRENT_DATE() AND status = 'approved' THEN 1 ELSE 0 END) as active_events
        FROM events 
        WHERE client_id = ?
    ");
    $stmt->execute([$client_id]);
    $eventStats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get total registrations and revenue
    $stmt = $conn->prepare("
        SELECT 
            COUNT(r.id) as total_registrations,
            COALESCE(SUM(p.amount), 0) as total_revenue
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        LEFT JOIN payments p ON r.id = p.registration_id
        WHERE e.client_id = ? AND p.status = 'completed'
    ");
    $stmt->execute([$client_id]);
    $regStats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get recent events (next 3 upcoming and last 3 past events)
    $stmt = $conn->prepare("
        SELECT 
            e.*,
            COUNT(DISTINCT r.id) as registration_count,
            CASE 
                WHEN e.event_date >= CURRENT_DATE() AND e.status = 'approved' THEN 'Active'
                WHEN e.event_date < CURRENT_DATE() THEN 'Past'
                WHEN e.status = 'draft' THEN 'Draft'
                WHEN e.status = 'pending' THEN 'Pending'
                ELSE e.status
            END as event_status
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        WHERE e.client_id = ?
        GROUP BY e.id
        ORDER BY e.event_date DESC
        LIMIT 6
    ");
    $stmt->execute([$client_id]);
    $recentEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "An error occurred while loading the dashboard.";
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Welcome, <?php echo htmlspecialchars($_SESSION['client']['name']); ?>!</h1>
            <p class="text-muted-foreground mt-2">Manage your events and track registrations.</p>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <div class="flex gap-4">
                <a href="create-event.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Event
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Events Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h2 class="text-sm font-medium tracking-tight">Total Events</h2>
                    <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold"><?php echo $eventStats['total_events']; ?></div>
                <p class="text-xs text-muted-foreground">Events created</p>
            </div>

            <!-- Active Events -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h2 class="text-sm font-medium tracking-tight">Active Events</h2>
                    <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold"><?php echo $eventStats['active_events']; ?></div>
                <p class="text-xs text-muted-foreground">Currently active</p>
            </div>

            <!-- Total Registrations -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h2 class="text-sm font-medium tracking-tight">Registrations</h2>
                    <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold"><?php echo $regStats['total_registrations']; ?></div>
                <p class="text-xs text-muted-foreground">Total registrations</p>
            </div>

            <!-- Revenue -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h2 class="text-sm font-medium tracking-tight">Revenue</h2>
                    <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold">$<?php echo number_format($regStats['total_revenue'], 2); ?></div>
                <p class="text-xs text-muted-foreground">Total revenue</p>
            </div>
        </div>

        <!-- Recent Events Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold tracking-tight">Recent Events</h2>
                <a href="events.php" class="text-sm text-primary hover:underline">View All Events</a>
            </div>
            
            <div class="rounded-lg border bg-card">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead>
                            <tr class="border-b transition-colors">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0 w-[300px]">
                                    Event
                                </th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                                    Date & Time
                                </th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                                    Registrations
                                </th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                                    Status
                                </th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentEvents)): ?>
                            <tr class="border-b transition-colors hover:bg-muted/50">
                                <td colspan="5" class="p-4 align-middle text-center text-muted-foreground">
                                    No events found
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($recentEvents as $event): ?>
                                <tr class="border-b transition-colors hover:bg-muted/50">
                                    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                                        <div class="flex space-y-1 flex-col">
                                            <span class="font-medium"><?php echo htmlspecialchars($event['title']); ?></span>
                                            <span class="text-sm text-muted-foreground"><?php echo htmlspecialchars($event['location']); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                                        <div class="flex flex-col">
                                            <span class="font-medium"><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                                            <span class="text-sm text-muted-foreground"><?php echo date('g:i A', strtotime($event['event_date'])); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-7 w-7 items-center justify-center rounded-lg border bg-muted">
                                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium"><?php echo $event['registration_count']; ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                                        <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo getStatusBadgeClasses($event['event_status']); ?>">
                                            <?php echo $event['event_status']; ?>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0">
                                        <div class="flex justify-end gap-2">
                                            <a href="edit-event.php?id=<?php echo $event['id']; ?>" 
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                                                title="Edit">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <a href="event-details.php?id=<?php echo $event['id']; ?>"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                                                title="View Details">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

<?php
function getStatusColor($status) {
    return match($status) {
        'Active' => 'text-green-600',
        'Past' => 'text-gray-600',
        'Draft' => 'text-yellow-600',
        'Pending' => 'text-blue-600',
        default => 'text-gray-600'
    };
}

function getStatusBadgeClasses($status) {
    return match($status) {
        'Active' => 'bg-green-100 text-green-800',
        'Past' => 'bg-gray-100 text-gray-800',
        'Draft' => 'bg-yellow-100 text-yellow-800',
        'Pending' => 'bg-blue-100 text-blue-800',
        default => 'bg-gray-100 text-gray-800'
    };
}
?> 