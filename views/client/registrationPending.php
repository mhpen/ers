<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

try {
    // Get pending registrations for the client's events
    $sql = "SELECT r.*, e.title as event_title, e.event_date, e.price,
            p.name as participant_name, p.email as participant_email,
            pay.status as payment_status, pay.payment_method, pay.reference_number, pay.proof_file
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN participants p ON r.participant_id = p.id
            LEFT JOIN payments pay ON r.id = pay.registration_id
            WHERE e.client_id = ? AND r.status = 'pending'
            ORDER BY r.registered_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['client']['id']]);
    $registrations = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching pending registrations: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load registrations.";
    header('Location: dashboard.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <div class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Pending Registrations</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (empty($registrations)): ?>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-600">No pending registrations found.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registration Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($reg['event_title']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($reg['event_date'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($reg['participant_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($reg['participant_email']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y g:i A', strtotime($reg['registered_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($reg['price'] > 0): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $reg['payment_status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($reg['payment_status'] ?? 'Pending'); ?>
                                            </span>
                                            <?php if ($reg['payment_status'] === 'pending' && $reg['proof_file']): ?>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Payment proof submitted
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">Free Event</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="view-registration.php?id=<?php echo $reg['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </a>
                                            <?php if ($reg['price'] > 0 && $reg['payment_status'] === 'pending' && $reg['proof_file']): ?>
                                                <a href="verify-payment.php?id=<?php echo $reg['id']; ?>" 
                                                   class="text-green-600 hover:text-green-900">
                                                    Verify Payment
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 