<?php
require_once '../../config/config.php';
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store form data in session for preview
    $_SESSION['registration_preview'] = [
        'event_id' => $_POST['event_id'] ?? null,
        'contact_number' => $_POST['contact_number'] ?? null,
        'emergency_contact' => $_POST['emergency_contact'] ?? null,
        'emergency_number' => $_POST['emergency_number'] ?? null,
        'notes' => $_POST['notes'] ?? null,
        'terms' => isset($_POST['terms'])
    ];

    // Redirect to preview page
    header("Location: ../../views/participant/payment-preview.php");
    exit();
} 