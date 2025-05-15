<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../config/config.php';

// Get event and registration data
$event_id = $_GET['event_id'] ?? null;
$registration_data = $_SESSION['registration_data'] ?? null;

if (!$event_id || !$registration_data) {
    header('Location: participantPage.php');
    exit();
}

// Get event details
try {
    $sql = "SELECT e.*, c.name as category_name, t.name as type_name, r.contact_number
            FROM events e
            LEFT JOIN categories c ON e.category_id = c.id
            LEFT JOIN event_types t ON e.type_id = t.id
            LEFT JOIN registrations r ON e.id = r.event_id AND r.participant_id = ?
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $event_id]);
    $event = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: participantPage.php');
    exit();
}

// At the top of the file, after session start
if (!isset($_SESSION['registration_data'])) {
    error_log("No registration data found in session");
    header('Location: participantPage.php');
    exit();
}

error_log("Payment page - Registration data: " . print_r($_SESSION['registration_data'], true));

include_once '../shared/header.php';
?>

<body class="bg-zinc-50 min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-md border border-zinc-200">
            <div class="p-8">
                <h2 class="text-2xl font-semibold text-zinc-900 mb-8">Complete Payment</h2>

                <!-- Event Summary -->
                <div class="bg-zinc-50 rounded-lg p-6 mb-8 border border-zinc-200">
                    <h3 class="font-semibold text-zinc-900 mb-2 text-lg"><?php echo htmlspecialchars($event['title']); ?></h3>
                </div>

                <!-- Payment Form -->
                <form action="../../controllers/participant/PaymentController.php" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <!-- Debug output -->
                    <?php
                    error_log("[DEBUG] Payment page - Registration data: " . print_r($_SESSION['registration_data'], true));
                    error_log("[DEBUG] Registration code in session: " . ($_SESSION['registration_data']['registration_code'] ?? 'not set'));
                    ?>

                    <input type="hidden" name="action" value="process_payment">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                    <!-- Add registration code to payment form -->
                    <input type="hidden" name="registration_code" value="<?php echo htmlspecialchars($_SESSION['registration_data']['registration_code'] ?? ''); ?>">

                    <!-- Payment Methods -->
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-zinc-900">Select Payment Method</h4>
                        <div class="space-y-4">
                            <!-- GCash -->
                            <div class="border border-zinc-200 rounded-lg p-5 hover:border-zinc-400 transition-colors">
                                <label class="flex items-center space-x-3">
                                    <input type="radio" name="payment_method" value="gcash" required
                                           class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                    <span class="text-zinc-900 font-medium">GCash</span>
                                </label>
                                <div class="mt-3 pl-7 text-sm text-zinc-600 space-y-1">
                                    <p>Send to: 09123456789</p>
                                    <p>Account Name: John Doe</p>
                                </div>
                            </div>

                            <!-- Bank Transfer -->
                            <div class="border border-zinc-200 rounded-lg p-5 hover:border-zinc-400 transition-colors">
                                <label class="flex items-center space-x-3">
                                    <input type="radio" name="payment_method" value="bank" required
                                           class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                    <span class="text-zinc-900 font-medium">Bank Transfer</span>
                                </label>
                                <div class="mt-3 pl-7 text-sm text-zinc-600 space-y-1">
                                    <p>Bank: BDO</p>
                                    <p>Account: 1234 5678 9012</p>
                                    <p>Account Name: John Doe</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add error message display -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Details -->
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-zinc-900">Reference Number</label>
                            <input type="text" name="reference_number" required
                                   class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm"
                                   placeholder="Enter payment reference number">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-zinc-900">Proof of Payment</label>
                            <input type="file" name="payment_proof" required accept="image/*"
                                   class="block w-full text-sm text-zinc-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-zinc-100 file:text-zinc-900
                                          hover:file:bg-zinc-200
                                          transition-colors">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="javascript:history.back()" 
                           class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md hover:bg-zinc-50 transition-colors">
                            Back
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-zinc-800 transition-colors">
                            Complete Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html> 