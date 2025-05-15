<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

try {
    // Get all payments for the client's events
    $sql = "SELECT p.*, r.status as registration_status, 
            e.title as event_title, e.event_date, e.price,
            part.name as participant_name, part.email as participant_email
            FROM payments p
            JOIN registrations r ON p.registration_id = r.id
            JOIN events e ON r.event_id = e.id
            JOIN participants part ON r.participant_id = part.id
            WHERE e.client_id = ?
            ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['client']['id']]);
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching payments: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load payments.";
    header('Location: dashboard.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-zinc-50 min-h-screen font-sans antialiased">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <div class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-zinc-900">Payments</h1>
                    <p class="text-sm text-zinc-500 mt-1">View and manage payments for your events</p>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (empty($payments)): ?>
                <div class="bg-white rounded-lg shadow-sm border border-zinc-200 p-6 text-center">
                    <p class="text-zinc-600">No payments found.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm border border-zinc-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200">
                            <thead class="bg-zinc-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Event/Participant
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Payment Details
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-zinc-200">
                                <?php foreach ($payments as $payment): ?>
                                    <tr class="hover:bg-zinc-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-zinc-900">
                                                <?php echo htmlspecialchars($payment['event_title']); ?>
                                            </div>
                                            <div class="text-sm text-zinc-500">
                                                <?php echo htmlspecialchars($payment['participant_name']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-zinc-900">
                                                <?php echo ucfirst($payment['payment_method']); ?>
                                            </div>
                                            <div class="text-sm text-zinc-500">
                                                Ref: <?php echo $payment['reference_number']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-zinc-900">
                                                ₱<?php echo number_format($payment['amount'], 2); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php echo $payment['status'] === 'confirmed' 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-500">
                                            <?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex space-x-3">
                                                <?php if ($payment['proof_file']): ?>
                                                    <button onclick="viewProof('<?php echo htmlspecialchars($payment['proof_file']); ?>')"
                                                            class="text-blue-600 hover:text-blue-800">
                                                        View Proof
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($payment['status'] === 'pending'): ?>
                                                    <button onclick="openVerifyModal(<?php 
                                                        echo htmlspecialchars(json_encode([
                                                            'id' => $payment['id'],
                                                            'event_title' => $payment['event_title'],
                                                            'participant_name' => $payment['participant_name'],
                                                            'amount' => number_format($payment['amount'], 2)
                                                        ])); 
                                                    ?>)" class="text-green-600 hover:text-green-800 font-medium">
                                                        Verify
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="proofModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
            
            <div class="relative bg-white rounded-lg max-w-3xl w-full">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button onclick="closeModal()" class="text-zinc-400 hover:text-zinc-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <h3 class="text-lg font-medium text-zinc-900 mb-4">Payment Proof</h3>
                    <div class="flex justify-center">
                        <img id="proofImage" src="" alt="Payment Proof" class="max-h-[70vh] object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal right after the payment proof modal -->
    <div id="verifyModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeVerifyModal()"></div>
            
            <div class="relative bg-white rounded-lg max-w-lg w-full">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button onclick="closeVerifyModal()" class="text-zinc-400 hover:text-zinc-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-4">Verify Payment</h3>
                    
                    <form id="verifyForm" action="../../controllers/client/PaymentController.php" method="POST">
                        <input type="hidden" name="action" value="verify_payment">
                        <input type="hidden" name="payment_id" id="verify_payment_id">

                        <!-- Payment Details -->
                        <div class="mb-6">
                            <div class="bg-zinc-50 rounded-lg p-4 space-y-2">
                                <div class="text-sm">
                                    <span class="text-zinc-500">Event:</span>
                                    <span id="verify_event_title" class="ml-2 text-zinc-900"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-zinc-500">Participant:</span>
                                    <span id="verify_participant_name" class="ml-2 text-zinc-900"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-zinc-500">Amount:</span>
                                    <span id="verify_amount" class="ml-2 text-zinc-900"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Options -->
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

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" 
                                    onclick="closeVerifyModal()"
                                    class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md hover:bg-zinc-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-zinc-800">
                                Submit Verification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewProof(filename) {
            const modal = document.getElementById('proofModal');
            const img = document.getElementById('proofImage');
            img.src = `../../uploads/payment_proofs/${filename}`;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('proofModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        function verifyPayment(paymentId) {
            const modal = document.getElementById('verifyModal');
            const verifyForm = document.getElementById('verifyForm');
            const verifyPaymentId = document.getElementById('verify_payment_id');
            const verifyEventTitle = document.getElementById('verify_event_title');
            const verifyParticipantName = document.getElementById('verify_participant_name');
            const verifyAmount = document.getElementById('verify_amount');

            verifyPaymentId.value = paymentId;
            verifyEventTitle.textContent = '<?php echo htmlspecialchars($payment['event_title']); ?>';
            verifyParticipantName.textContent = '<?php echo htmlspecialchars($payment['participant_name']); ?>';
            verifyAmount.textContent = '₱<?php echo number_format($payment['amount'], 2); ?>';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeVerifyModal() {
            const modal = document.getElementById('verifyModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form
            document.getElementById('verifyForm').reset();
        }

        // Update the existing event listener to handle both modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeVerifyModal();
            }
        });

        function openVerifyModal(paymentData) {
            const modal = document.getElementById('verifyModal');
            document.getElementById('verify_payment_id').value = paymentData.id;
            document.getElementById('verify_event_title').textContent = paymentData.event_title;
            document.getElementById('verify_participant_name').textContent = paymentData.participant_name;
            document.getElementById('verify_amount').textContent = '₱' + paymentData.amount;
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    </script>
</body>
</html>
