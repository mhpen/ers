<?php
require_once '../../helpers/SessionHelper.php';
require_once '../../config/config.php';
require_once '../../models/Participant.php';

SessionHelper::requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participantModel = new Participant($conn);

    if ($_POST['action'] === 'toggleStatus') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($id && $participantModel->toggleStatus($id)) {
            SessionHelper::setFlashMessage('success', 'Participant status updated successfully.');
        } else {
            SessionHelper::setFlashMessage('error', 'Failed to update participant status.');
        }
    }
    
    header('Location: ../../views/admin/participants.php');
    exit();
} 