<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';
include_once '../shared/header.php';
?>

<body class="bg-background">
    <div class="flex h-screen">
        <?php include '../shared/clientSidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto bg-background">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight">Registrations</h1>
                        <p class="text-sm text-muted-foreground">Manage event registrations and participants</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="rounded-lg border bg-card text-card-foreground mb-6">
                    <div class="p-6">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="text-sm font-medium mb-2 block">Event</label>
                                <select id="eventFilter" onchange="filterRegistrations()" 
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">All Events</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT id, title FROM events WHERE client_id = ? ORDER BY event_date DESC");
                                    $stmt->execute([$_SESSION['client']['id']]);
                                    while ($event = $stmt->fetch()) {
                                        echo "<option value='{$event['id']}'>{$event['title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="text-sm font-medium mb-2 block">Status</label>
                                <select id="statusFilter" onchange="filterRegistrations()" 
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="text-sm font-medium mb-2 block">Search</label>
                                <input type="text" id="searchInput" onkeyup="filterRegistrations()" placeholder="Search participants..." 
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registrations Table -->
                <div class="rounded-lg border bg-card text-card-foreground">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Event</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Participant</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Registration Date</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <?php
                                $sql = "SELECT r.*, e.title as event_title, p.name as participant_name, p.email 
                                       FROM registrations r
                                       JOIN events e ON r.event_id = e.id
                                       JOIN participants p ON r.participant_id = p.id
                                       WHERE e.client_id = ?
                                       ORDER BY r.registered_at DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION['client']['id']]);
                                
                                while ($row = $stmt->fetch()) {
                                    $statusClass = match($row['status']) {
                                        'confirmed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/20'
                                    };
                                    ?>
                                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                        <td class="p-4 align-middle"><?= htmlspecialchars($row['event_title']) ?></td>
                                        <td class="p-4 align-middle">
                                            <div><?= htmlspecialchars($row['participant_name']) ?></div>
                                            <div class="text-sm text-muted-foreground"><?= htmlspecialchars($row['email']) ?></div>
                                        </td>
                                        <td class="p-4 align-middle"><?= date('M j, Y', strtotime($row['registered_at'])) ?></td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset <?= $statusClass ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <a href="view-registration.php?id=<?= $row['id'] ?>" 
                                               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterRegistrations() {
            const eventFilter = document.getElementById('eventFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const searchFilter = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const event = row.cells[0].textContent.toLowerCase();
                const participant = row.cells[1].textContent.toLowerCase();
                const status = row.cells[3].textContent.toLowerCase();

                const matchesEvent = !eventFilter || event.includes(eventFilter);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                const matchesSearch = !searchFilter || 
                    participant.includes(searchFilter) || 
                    event.includes(searchFilter);

                row.style.display = matchesEvent && matchesStatus && matchesSearch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 