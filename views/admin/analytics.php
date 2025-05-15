<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../models/Admin.php';
$adminModel = new Admin($conn);
$analytics = $adminModel->getAnalytics();

include_once '../shared/header.php';
?>

<head>
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Analytics</h1>
            <p class="text-muted-foreground mt-2">View system analytics and reports.</p>
        </div>

        <!-- Analytics Overview -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-8">
            <!-- Registration Trends -->
            <div class="rounded-lg border bg-card p-6">
                <h3 class="font-semibold mb-4">Registration Trends</h3>
                <div class="h-[300px]">
                    <canvas id="registrationTrends"></canvas>
                </div>
            </div>

            <!-- Popular Event Types -->
            <div class="rounded-lg border bg-card p-6">
                <h3 class="font-semibold mb-4">Popular Event Types</h3>
                <div class="h-[300px]">
                    <canvas id="eventTypesPie"></canvas>
                </div>
            </div>

            <!-- Monthly Growth -->
            <div class="rounded-lg border bg-card p-6">
                <h3 class="font-semibold mb-4">Monthly Growth</h3>
                <div class="h-[300px]">
                    <canvas id="monthlyGrowth"></canvas>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <?php 
            $latest = !empty($analytics['monthly_stats']) ? $analytics['monthly_stats'][0] : null;
            $previous = !empty($analytics['monthly_stats'][1]) ? $analytics['monthly_stats'][1] : null;
            
            if ($latest && $previous):
                $userGrowth = (($latest['new_users'] - $previous['new_users']) / $previous['new_users']) * 100;
                $eventGrowth = (($latest['events_created'] - $previous['events_created']) / $previous['events_created']) * 100;
            ?>
                <!-- New Users KPI -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-sm font-medium text-muted-foreground">New Users</h3>
                    <div class="mt-2 flex items-center">
                        <span class="text-2xl font-bold"><?php echo $latest['new_users']; ?></span>
                        <span class="ml-2 text-sm <?php echo $userGrowth >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo sprintf('%.1f%%', $userGrowth); ?>
                        </span>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">vs previous month</p>
                </div>

                <!-- Events Created KPI -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-sm font-medium text-muted-foreground">Events Created</h3>
                    <div class="mt-2 flex items-center">
                        <span class="text-2xl font-bold"><?php echo $latest['events_created']; ?></span>
                        <span class="ml-2 text-sm <?php echo $eventGrowth >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo sprintf('%.1f%%', $eventGrowth); ?>
                        </span>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">vs previous month</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Detailed Monthly Stats Table -->
        <div class="rounded-lg border bg-card p-6">
            <h3 class="font-semibold mb-4">Monthly Statistics</h3>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left pb-4">Month</th>
                        <th class="text-left pb-4">New Users</th>
                        <th class="text-left pb-4">Events Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($analytics['monthly_stats'] as $stat): ?>
                        <tr class="border-b">
                            <td class="py-4"><?php echo date('F Y', strtotime($stat['month'])); ?></td>
                            <td class="py-4"><?php echo $stat['new_users']; ?></td>
                            <td class="py-4"><?php echo $stat['events_created']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Prepare data for charts
        const registrationData = <?php echo json_encode($analytics['registration_trends']); ?>;
        const eventTypesData = <?php echo json_encode($analytics['popular_event_types']); ?>;
        const monthlyStats = <?php echo json_encode($analytics['monthly_stats']); ?>;

        // Registration Trends Line Chart
        new Chart(document.getElementById('registrationTrends'), {
            type: 'line',
            data: {
                labels: registrationData.map(item => {
                    const date = new Date(item.month);
                    return date.toLocaleDateString('default', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'New Registrations',
                    data: registrationData.map(item => item.count),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Event Types Pie Chart
        new Chart(document.getElementById('eventTypesPie'), {
            type: 'doughnut',
            data: {
                labels: eventTypesData.map(item => item.type_name),
                datasets: [{
                    data: eventTypesData.map(item => item.event_count),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Monthly Growth Bar Chart
        new Chart(document.getElementById('monthlyGrowth'), {
            type: 'bar',
            data: {
                labels: monthlyStats.map(item => {
                    const date = new Date(item.month);
                    return date.toLocaleDateString('default', { month: 'short', year: 'numeric' });
                }).reverse(),
                datasets: [{
                    label: 'New Users',
                    data: monthlyStats.map(item => item.new_users).reverse(),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }, {
                    label: 'Events Created',
                    data: monthlyStats.map(item => item.events_created).reverse(),
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 