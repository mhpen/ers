<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../models/Event.php';
require_once '../../config/config.php';

// Add debug logging
error_log("Session data: " . json_encode($_SESSION));
error_log("Event ID: " . $_GET['event_id']);

// Get event details
$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    error_log("No event ID provided");
    header('Location: participantPage.php');
    exit();
}

try {
    $sql = "SELECT e.*, c.name as category_name, t.name as type_name,
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_participants
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

    $availableSlots = $event['slots'] - ($event['registered_participants'] ?? 0);
    if ($availableSlots <= 0) {
        $_SESSION['error'] = "This event is already full.";
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

<body class="bg-zinc-50 min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-md border border-zinc-200">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-semibold text-zinc-900">Event Registration</h2>
                    <a href="participantPage.php" 
                       class="text-zinc-500 hover:text-zinc-900 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>

                <!-- Event Details -->
                <div class="bg-zinc-50 rounded-lg p-6 mb-8 border border-zinc-200">
                    <h3 class="font-semibold text-zinc-900 mb-4 text-lg"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-500">Date</p>
                            <p class="font-medium text-zinc-900"><?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-500">Event Type</p>
                            <p class="font-medium text-zinc-900"><?php echo htmlspecialchars($event['type_name']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-500">Available Slots</p>
                            <p class="font-medium text-zinc-900"><?php echo $availableSlots; ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-500">Price</p>
                            <p class="font-medium text-zinc-900"><?php echo $event['price'] > 0 ? '₱' . number_format($event['price'], 2) : 'Free'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <form action="../../controllers/participant/RegistrationController.php" method="POST" class="space-y-8">
                    <?php 
                    // Generate registration code
                    $registration_code = 'REG-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                    $_SESSION['temp_registration_code'] = $registration_code; // Store in session temporarily
                    ?>
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                    <input type="hidden" name="registration_code" value="<?php echo htmlspecialchars($registration_code); ?>">

                    <!-- Contact Information -->
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-zinc-900">Contact Information</h4>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-zinc-900">
                                    Contact Number
                                </label>
                                <input type="tel" 
                                       name="contact_number" 
                                       required
                                       pattern="[0-9]{11}"
                                       placeholder="09XXXXXXXXX"
                                       class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-900 mb-1">
                                    Emergency Contact Person
                                </label>
                                <input type="text" 
                                       name="emergency_contact" 
                                       required
                                       placeholder="Full Name"
                                       class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-900 mb-1">
                                    Emergency Contact Number
                                </label>
                                <input type="tel" 
                                       name="emergency_number" 
                                       required
                                       pattern="[0-9]{11}"
                                       placeholder="09XXXXXXXXX"
                                       class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <h4 class="text-lg font-medium text-zinc-900 mb-4">Additional Information</h4>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">
                                Special Requirements/Notes (Optional)
                            </label>
                            <textarea name="notes" 
                                    rows="3" 
                                    class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-black focus:ring-black sm:text-sm"
                                    placeholder="Any dietary restrictions, accessibility needs, or other special requirements"></textarea>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="bg-zinc-50 p-6 rounded-lg border border-zinc-200">
                        <h4 class="font-semibold text-zinc-900 mb-3">Terms and Conditions</h4>
                        <div class="text-sm text-zinc-600 space-y-2 mb-4">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Registration is non-transferable</li>
                                <li>Payment must be completed within 24 hours</li>
                                <li>Cancellation policy applies</li>
                                <li>You agree to follow event guidelines</li>
                            </ul>
                        </div>
                        <div class="flex items-start mt-4">
                            <div class="flex items-center h-5">
                                <input id="terms" 
                                       name="terms" 
                                       type="checkbox" 
                                       required
                                       class="h-4 w-4 text-black focus:ring-black border-zinc-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="terms" class="text-sm text-zinc-600">
                                    I agree to the terms and conditions
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-3">
                        <a href="participantPage.php" 
                           class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md hover:bg-zinc-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 text-sm font-medium text-white bg-black border border-transparent rounded-md hover:bg-zinc-800 transition-colors">
                            Next: Payment Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>