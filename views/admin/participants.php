<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../models/Participant.php';
$participantModel = new Participant($conn);

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$sort = $_GET['sort'] ?? 'newest';

// Get participants with filters
$participants = $participantModel->getFilteredParticipants($search, $status, $sort);

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Participants</h1>
            <p class="text-muted-foreground mt-2">Manage event participants and registrations.</p>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by name or email..." 
                           class="w-full h-10 rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                </div>
                <select name="status" class="h-10 rounded-md border px-3 py-2 text-sm">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <select name="sort" class="h-10 rounded-md border px-3 py-2 text-sm">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                    <option value="email" <?php echo $sort === 'email' ? 'selected' : ''; ?>>Email A-Z</option>
                </select>
                <button type="submit" class="btn-primary px-4 py-2 rounded-md">Search</button>
            </form>
        </div>

        <!-- Participants List -->
        <div class="rounded-lg border bg-card">
            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left pb-4">Name</th>
                            <th class="text-left pb-4">Email</th>
                            <th class="text-left pb-4">Phone</th>
                            <th class="text-left pb-4">Status</th>
                            <th class="text-left pb-4">Registered</th>
                            <th class="text-left pb-4">Last Login</th>
                            <th class="text-right pb-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($participants)): ?>
                            <tr>
                                <td colspan="7" class="py-4 text-center text-muted-foreground">No participants found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($participants as $participant): ?>
                                <tr class="border-b">
                                    <td class="py-4"><?php echo htmlspecialchars($participant['name']); ?></td>
                                    <td class="py-4"><?php echo htmlspecialchars($participant['email']); ?></td>
                                    <td class="py-4"><?php echo htmlspecialchars($participant['phone'] ?? 'N/A'); ?></td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            <?php echo $participant['status'] === 'active' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($participant['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4"><?php echo date('M d, Y', strtotime($participant['created_at'])); ?></td>
                                    <td class="py-4">
                                        <?php echo $participant['last_login'] 
                                            ? date('M d, Y H:i', strtotime($participant['last_login'])) 
                                            : 'Never'; ?>
                                    </td>
                                    <td class="py-4 text-right space-x-2">
                                        <button onclick="viewParticipant(<?php echo $participant['id']; ?>)"
                                                class="text-blue-600 hover:text-blue-800">View</button>
                                        <button onclick="toggleStatus(<?php echo $participant['id']; ?>, '<?php echo $participant['status']; ?>')"
                                                class="<?php echo $participant['status'] === 'active' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'; ?>">
                                            <?php echo $participant['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    function viewParticipant(id) {
        window.location.href = `participantDetails.php?id=${id}`;
    }

    function toggleStatus(id, currentStatus) {
        if (confirm(`Are you sure you want to ${currentStatus === 'active' ? 'deactivate' : 'activate'} this participant?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../controllers/admin/ParticipantController.php';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'toggleStatus';

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;

            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html> 