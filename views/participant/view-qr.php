<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');
require_once '../../config/config.php';

$registration_id = $_GET['id'] ?? null;

if (!$registration_id) {
    header('Location: my-registrations.php');
    exit();
}

try {
    $sql = "SELECT r.*, e.title as event_title, e.event_date
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            WHERE r.id = ? AND r.participant_id = ? AND r.status = 'confirmed'";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$registration_id, $_SESSION['participant_id']]);
    $registration = $stmt->fetch();

    if (!$registration) {
        header('Location: my-registrations.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Error fetching registration: " . $e->getMessage());
    header('Location: my-registrations.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/participantNavbar.php'; ?>

    <main class="max-w-lg mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Registration QR Code</h2>
                
                <div class="text-center">
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($registration['event_title']); ?></h3>
                        <p class="text-gray-600"><?php echo date('F j, Y', strtotime($registration['event_date'])); ?></p>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-gray-100 p-4 rounded-lg mb-4">
                        <div class="bg-white p-4 inline-block rounded">
                            <!-- You can use a QR code library to generate the actual QR code -->
                            <img src="generate-qr.php?code=<?php echo urlencode($registration['qr_code']); ?>" 
                                 alt="QR Code" 
                                 class="w-48 h-48">
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <?php echo $registration['qr_code']; ?>
                        </div>
                    </div>

                    <a href="my-registrations.php" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Back to Registrations
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 