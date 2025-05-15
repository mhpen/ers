<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
SessionHelper::requireLogin('admin');

require_once '../../models/ClientManager.php';
$clientManager = new ClientManager($conn);
$clients = $clientManager->getAllClients();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Client Management</h1>
                <p class="text-muted-foreground mt-2">Manage clients and review registration requests.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           placeholder="Search clients..." 
                           class="pl-10 pr-4 py-2 h-10 rounded-md border bg-white text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                </div>
                <select class="h-10 rounded-md border bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
        </div>

        <?php include_once '../shared/alerts.php'; ?>

        <!-- Client List -->
        <div class="rounded-lg border bg-white shadow-sm">
            <div class="p-6">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 rounded-lg">
                            <tr>
                                <th class="px-6 py-4 font-medium text-gray-500">Organization</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Contact Person</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Email</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Registration Date</th>
                                <th class="px-6 py-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 py-4 font-medium text-gray-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <span class="text-lg font-medium text-gray-600">
                                                    <?php echo strtoupper(substr($client['organization'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-medium"><?php echo htmlspecialchars($client['organization']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($client['name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($client['created_at'])); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo $client['approved'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo $client['approved'] ? 'Approved' : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="clientDetails.php?id=<?php echo $client['id']; ?>" 
                                               class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-gray-200 hover:bg-gray-100 h-9 px-4">
                                                View Details
                                            </a>
                                            
                                            <?php if (!$client['approved']): ?>
                                                <form method="POST" action="../../controllers/admin/ClientController.php" class="inline">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                                                    <button type="submit" 
                                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-green-600 text-white hover:bg-green-700 h-9 px-4">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="../../controllers/admin/ClientController.php" class="inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to reject this client?')"
                                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-red-600 text-white hover:bg-red-700 h-9 px-4">
                                                        Reject
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 