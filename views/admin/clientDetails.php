<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: adminLogin.php');
    exit();
}

require_once '../../config/config.php';
require_once '../../models/ClientManager.php';

if (!isset($_GET['id'])) {
    header('Location: clients.php?error=Client ID not provided');
    exit();
}

$clientManager = new ClientManager($conn);
$client = $clientManager->getClientById($_GET['id']);

if (!$client) {
    header('Location: clients.php?error=Client not found');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Client Details</h1>
                <p class="text-muted-foreground mt-2">Detailed information and management options for <?php echo htmlspecialchars($client['organization']); ?></p>
            </div>
            <a href="clients.php" class="btn-secondary px-4 py-2 rounded-md">Back to Clients</a>
        </div>

        <!-- Client Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="font-medium">Organization</label>
                        <p><?php echo htmlspecialchars($client['organization']); ?></p>
                    </div>
                    <div>
                        <label class="font-medium">Contact Person</label>
                        <p><?php echo htmlspecialchars($client['name']); ?></p>
                    </div>
                    <div>
                        <label class="font-medium">Email</label>
                        <p><?php echo htmlspecialchars($client['email']); ?></p>
                    </div>
                    <div>
                        <label class="font-medium">Registration Date</label>
                        <p><?php echo date('F d, Y', strtotime($client['created_at'])); ?></p>
                    </div>
                    <div>
                        <label class="font-medium">Status</label>
                        <p>
                            <?php if ($client['approved']): ?>
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-4">Management Actions</h2>
                <div class="space-y-4">
                    <?php if (!$client['approved']): ?>
                        <form method="POST" action="../../controllers/admin/ClientController.php" class="space-y-4">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                            <input type="hidden" name="return_to_details" value="1">
                            <button type="submit" class="w-full btn-primary px-4 py-2 rounded-md">
                                Approve Client
                            </button>
                        </form>
                        <form method="POST" action="../../controllers/admin/ClientController.php" class="space-y-4">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                            <button type="submit" 
                                    class="w-full btn-danger px-4 py-2 rounded-md"
                                    onclick="return confirm('Are you sure you want to reject this client? This action cannot be undone.')">
                                Reject Client
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="../../controllers/admin/ClientController.php" class="space-y-4">
                            <input type="hidden" name="action" value="suspend">
                            <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                            <input type="hidden" name="return_to_details" value="1">
                            <button type="submit" 
                                    class="w-full btn-warning px-4 py-2 rounded-md"
                                    onclick="return confirm('Are you sure you want to suspend this client?')">
                                Suspend Client
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 