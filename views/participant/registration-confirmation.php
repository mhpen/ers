<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../config/config.php';

$registration_id = $_GET['id'] ?? null;
if (!$registration_id) {
    header('Location: participantPage.php');
    exit();
}

try {
    $sql = "SELECT r.*, e.title, e.event_date, e.price, r.status as registration_status
            FROM registrations r 
            JOIN events e ON r.event_id = e.id 
            WHERE r.id = ? AND r.participant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$registration_id, $_SESSION['participant_id']]);
    $registration = $stmt->fetch();

    if (!$registration) {
        header('Location: participantPage.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching registration: " . $e->getMessage());
    header('Location: participantPage.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Registration Successful!</h2>
                    <p class="text-gray-600">
                        <?php if ($registration['price'] > 0 && $registration['status'] === 'pending'): ?>
                            Please complete your payment to secure your slot.
                        <?php else: ?>
                            Your registration has been confirmed.
                        <?php endif; ?>
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4"><?php echo htmlspecialchars($registration['title']); ?></h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Registration ID</p>
                            <p class="font-medium">#<?php echo $registration['id']; ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Event Date</p>
                            <p class="font-medium"><?php echo date('F j, Y', strtotime($registration['event_date'])); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <p class="font-medium">
                                <?php if ($registration['status'] === 'confirmed'): ?>
                                    <span class="text-green-600">Confirmed</span>
                                <?php else: ?>
                                    <span class="text-yellow-600">Pending Payment</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Amount</p>
                            <p class="font-medium"><?php echo $registration['price'] > 0 ? '₱' . number_format($registration['price'], 2) : 'Free'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-4">
                    <a href="participantPage.php" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Events
                    </a>
                    <?php if ($registration['price'] > 0 && $registration['status'] === 'pending'): ?>
                        <a href="payment.php?registration_id=<?php echo $registration_id; ?>" 
                           class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Complete Payment
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 