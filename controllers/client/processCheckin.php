<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['event_id']) || !isset($data['registration_code'])) {
        throw new Exception('Missing required parameters');
    }

    $event_id = $data['event_id'];
    $registration_code = $data['registration_code'];

    // Verify the event belongs to the current client
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND client_id = ?");
    $stmt->execute([$event_id, $_SESSION['client']['id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid event');
    }

    // Get participant registration
    $stmt = $conn->prepare("
        SELECT r.participant_id, r.id as registration_id, p.name as participant_name 
        FROM registrations r
        JOIN participants p ON p.id = r.participant_id
        WHERE r.registration_code = ? AND r.event_id = ?
    ");
    $stmt->execute([$registration_code, $event_id]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception('Invalid registration code');
    }

    // Check if already checked in
    $stmt = $conn->prepare("
        SELECT checkin_id FROM checkins 
        WHERE participant_id = ? AND event_id = ?
    ");
    $stmt->execute([$registration['participant_id'], $event_id]);
    if ($stmt->fetch()) {
        throw new Exception('Participant already checked in');
    }

    // Process check-in
    $stmt = $conn->prepare("
        INSERT INTO checkins (participant_id, event_id, checkin_time, status) 
        VALUES (?, ?, NOW(), 'checked_in')
    ");
    $stmt->execute([$registration['participant_id'], $event_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Check-in successful for ' . $registration['participant_name']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 