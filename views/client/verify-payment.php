<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

$payment_id = $_GET['id'] ?? null;

try {
    $sql = "SELECT p.*, r.status as registration_status, 
            e.title as event_title, e.event_date, e.price,
            part.name as participant_name, part.email as participant_email
            FROM payments p
            JOIN registrations r ON p.registration_id = r.id
            JOIN events e ON r.event_id = e.id
            JOIN participants part ON r.participant_id = part.id
            WHERE p.id = ? AND e.client_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$payment_id, $_SESSION['client']['id']]);
    $payment = $stmt->fetch();

    if (!$payment) {
        $_SESSION['error'] = "Payment not found.";
        header('Location: payments.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: payments.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-zinc-50 min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <div class="ml-64 p-8">
        <div class="max-w-3xl mx-auto">
            <!-- Add verification form here -->
            <div class="bg-white rounded-lg shadow-sm border border-zinc-200 p-6">
                <h2 class="text-xl font-semibold mb-6">Verify Payment</h2>
                
                <form action="../../controllers/client/PaymentController.php" method="POST">
                    <input type="hidden" name="action" value="verify_payment">
                    <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">

                    <div class="space-y-6">
                        <!-- Payment details -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Add payment details here -->
                        </div>

                        <!-- Verification options -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-900">Verification Status</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio" name="status" value="confirmed" required
                                               class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                        <label class="ml-2 text-sm text-zinc-900">Confirm Payment</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="status" value="declined" required
                                               class="h-4 w-4 text-black focus:ring-black border-zinc-300">
                                        <label class="ml-2 text-sm text-zinc-900">Decline Payment</label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-900">Remarks</label>
                                <textarea name="remarks" rows="3" 
                                    class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm"
                                    placeholder="Add any comments or reasons for declining"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="payments.php" 
                               class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md hover:bg-zinc-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-zinc-800">
                                Submit Verification
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 