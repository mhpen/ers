<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

$registration_id = $_GET['id'] ?? null;

if (!$registration_id) {
    header('Location: registrations.php');
    exit();
}

try {
    // Get registration details with related information
    $sql = "SELECT r.*, 
            e.title as event_title, e.event_date, e.price, e.description as event_description,
            p.name as participant_name, p.email as participant_email,
            pay.status as payment_status, pay.payment_method, pay.reference_number, 
            pay.proof_file, pay.created_at as payment_date
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN participants p ON r.participant_id = p.id
            LEFT JOIN payments pay ON r.id = pay.registration_id
            WHERE r.id = ? AND e.client_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$registration_id, $_SESSION['client']['id']]);
    $registration = $stmt->fetch();

    if (!$registration) {
        $_SESSION['error'] = "Registration not found.";
        header('Location: registrations.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Error fetching registration: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load registration details.";
    header('Location: registrations.php');
    exit();
}

include_once '../shared/header.php';
?>

<style>
    /* Modal image container */
    #paymentProofModal img {
        max-height: 70vh;
        object-fit: contain;
        margin: 0 auto;
    }

    /* Prevent background scrolling when modal is open */
    body.modal-open {
        overflow: hidden;
    }

    /* Ensure modal is centered */
    .modal-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
</style>

<body class="bg-background min-h-screen">
    <?php include_once '../shared/clientSidebar.php'; ?>

    <div class="ml-64 p-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <a href="registrations.php" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Registrations
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Registration Details</h1>

                    <!-- Event Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Event Name</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['event_title']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Event Date</p>
                                    <p class="mt-1"><?php echo date('F j, Y', strtotime($registration['event_date'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Price</p>
                                    <p class="mt-1"><?php echo $registration['price'] > 0 ? '₱' . number_format($registration['price'], 2) : 'Free'; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Registration Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $registration['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo ucfirst($registration['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participant Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Participant Information</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['participant_name']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['participant_email']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Contact Number</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['contact_number']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Emergency Contact</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['emergency_contact']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Emergency Number</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($registration['emergency_number']); ?></p>
                                </div>
                            </div>
                            <?php if ($registration['notes']): ?>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-500">Additional Notes</p>
                                    <p class="mt-1"><?php echo nl2br(htmlspecialchars($registration['notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <?php if ($registration['price'] > 0): ?>
                        <div class="mb-8">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <?php if ($registration['payment_status']): ?>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Payment Status</p>
                                            <p class="mt-1">
                                                <span class="<?php echo $registration['payment_status'] === 'confirmed' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                                    <?php echo ucfirst($registration['payment_status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                            <p class="mt-1"><?php echo ucfirst($registration['payment_method'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Reference Number</p>
                                            <p class="mt-1"><?php echo $registration['reference_number'] ?? 'N/A'; ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Payment Date</p>
                                            <p class="mt-1"><?php echo $registration['payment_date'] ? date('F j, Y g:i A', strtotime($registration['payment_date'])) : 'N/A'; ?></p>
                                        </div>
                                    </div>
                                    <?php if ($registration['proof_file']): ?>
                                        <div class="mt-4">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Payment Proof</p>
                                            <button onclick="openModal()"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Payment Proof
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-gray-500">No payment information available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <?php if ($registration['status'] === 'pending' && $registration['payment_status'] === 'pending'): ?>
                            <a href="verify-payment.php?id=<?php echo $registration_id; ?>" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Verify Payment
                            </a>
                        <?php endif; ?>
                        <a href="registrations.php" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the modal at the end of the body tag, before closing -->
    <!-- Modal -->
    <div id="paymentProofModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Payment Proof
                            </h3>
                            <div class="mt-2">
                                <img src="../../uploads/payment_proofs/<?php echo $registration['proof_file']; ?>" 
                                     alt="Payment Proof" 
                                     class="max-w-full h-auto rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for modal functionality -->
    <script>
    const modal = document.getElementById('paymentProofModal');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Close modal on escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
    </script>
</body>
</html> 