<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../config/config.php';

// Get participant's registrations
try {
    $sql = "SELECT r.*, e.title as event_title, e.event_date, e.price,
            p.status as payment_status, p.payment_method, p.reference_number
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            LEFT JOIN payments p ON r.id = p.registration_id
            WHERE r.participant_id = ?
            ORDER BY r.registered_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['participant_id']]);
    $registrations = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching registrations: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load registrations.";
    header('Location: participantPage.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">My Registrations</h2>

                <?php if (empty($registrations)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-600">You haven't registered for any events yet.</p>
                        <a href="participantPage.php" 
                           class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Browse Events
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Event
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        QR Code
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo date('F j, Y', strtotime($reg['event_date'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo $reg['price'] > 0 ? '₱' . number_format($reg['price'], 2) : 'Free'; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $reg['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($reg['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($reg['price'] > 0): ?>
                                                <div class="text-sm text-gray-900">
                                                    <?php if ($reg['payment_status'] === 'pending'): ?>
                                                        <span class="text-yellow-600">Payment Pending</span>
                                                    <?php elseif ($reg['payment_status'] === 'confirmed'): ?>
                                                        <div>
                                                            <div><?php echo ucfirst($reg['payment_method']); ?></div>
                                                            <div class="text-xs text-gray-500">Ref: <?php echo $reg['reference_number']; ?></div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-500">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($reg['status'] === 'confirmed'): ?>
                                                <a href="view-qr.php?id=<?php echo $reg['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    View QR
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-500">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 