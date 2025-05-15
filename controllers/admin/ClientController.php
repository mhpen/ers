<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/ClientManager.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../views/admin/adminLogin.php');
    exit();
}

$clientManager = new ClientManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $clientId = $_POST['client_id'] ?? '';
    $adminId = $_SESSION['admin']['id'];
    $returnToDetails = isset($_POST['return_to_details']);

    if (!$clientId) {
        header('Location: ../../views/admin/clients.php?error=Invalid client ID');
        exit();
    }

    $success = false;
    $message = '';

    switch ($action) {
        case 'approve':
            $success = $clientManager->approveClient($clientId, $adminId);
            $message = $success ? 'Client approved successfully' : 'Failed to approve client';
            break;

        case 'reject':
            $success = $clientManager->rejectClient($clientId, $adminId);
            $message = $success ? 'Client rejected successfully' : 'Failed to reject client';
            // After rejection, always return to main client list since the client is deleted
            $returnToDetails = false;
            break;

        case 'suspend':
            $success = $clientManager->suspendClient($clientId, $adminId);
            $message = $success ? 'Client suspended successfully' : 'Failed to suspend client';
            break;

        default:
            header('Location: ../../views/admin/clients.php?error=Invalid action');
            exit();
    }

    $redirectParam = $success ? 'success' : 'error';
    
    if ($returnToDetails && $action !== 'reject') {
        header("Location: ../../views/admin/clientDetails.php?id={$clientId}&{$redirectParam}=" . urlencode($message));
    } else {
        header("Location: ../../views/admin/clients.php?{$redirectParam}=" . urlencode($message));
    }
    exit();
}

// If not POST request, redirect back to clients page
header('Location: ../../views/admin/clients.php');
exit(); 