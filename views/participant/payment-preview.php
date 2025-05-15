<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../config/config.php';

// Get preview data from session
$preview = $_SESSION['registration_preview'] ?? null;
if (!$preview) {
    header('Location: participantPage.php');
    exit();
}

$event_id = $preview['event_id'];
$contact_number = $preview['contact_number'];
$emergency_contact = $preview['emergency_contact'];
$emergency_number = $preview['emergency_number'];
$notes = $preview['notes'];
$terms = $preview['terms'];

// Validate required fields
if (!$event_id || !$contact_number || !$emergency_contact || !$emergency_number || !$terms) {
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: register-event.php?event_id=" . $event_id);
    exit();
}

// Get event details
try {
    $sql = "SELECT e.*, c.name as category_name, t.name as type_name
            FROM events e
            LEFT JOIN categories c ON e.category_id = c.id
            LEFT JOIN event_types t ON e.type_id = t.id
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        header('Location: participantPage.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching event: " . $e->getMessage());
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
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Review Registration</h2>
                </div>

                <!-- Event Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <div class="text-sm text-gray-600">
                        <p class="mb-2">Amount to Pay: <span class="font-medium text-gray-900">
                            <?php echo $event['price'] > 0 ? '₱' . number_format($event['price'], 2) : 'Free'; ?>
                        </span></p>
                    </div>
                </div>

                <!-- Registration Details -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Registration Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Contact Number</p>
                                <p class="font-medium"><?php echo htmlspecialchars($contact_number); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Emergency Contact</p>
                                <p class="font-medium"><?php echo htmlspecialchars($emergency_contact); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Emergency Number</p>
                                <p class="font-medium"><?php echo htmlspecialchars($emergency_number); ?></p>
                            </div>
                            <?php if ($notes): ?>
                            <div class="col-span-2">
                                <p class="text-gray-600">Special Requirements/Notes</p>
                                <p class="font-medium"><?php echo htmlspecialchars($notes); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Submit Form -->
                    <form action="../../controllers/participant/RegistrationController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                        <input type="hidden" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>">
                        <input type="hidden" name="emergency_contact" value="<?php echo htmlspecialchars($emergency_contact); ?>">
                        <input type="hidden" name="emergency_number" value="<?php echo htmlspecialchars($emergency_number); ?>">
                        <input type="hidden" name="notes" value="<?php echo htmlspecialchars($notes ?? ''); ?>">
                        <input type="hidden" name="terms" value="1">

                        <div class="flex justify-end gap-3 mt-6">
                            <a href="javascript:history.back()" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Back
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Confirm Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 