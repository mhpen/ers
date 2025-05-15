<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['event_id'])) {
        throw new Exception('Event ID is required');
    }

    $eventId = $_GET['event_id'];
    $clientId = $_SESSION['client']['id'];

    // First verify the event belongs to this client
    $sql = "SELECT id FROM events WHERE id = ? AND client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$eventId, $clientId]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Event not found or unauthorized');
    }

    // Get check-ins for the event
    $sql = "SELECT 
                c.checkin_id,
                c.checkin_time,
                c.status,
                p.name as participant_name,
                p.email as participant_email,
                r.id as registration_id
            FROM checkins c
            JOIN registrations r ON c.participant_id = r.participant_id AND c.event_id = r.event_id
            JOIN participants p ON c.participant_id = p.id
            WHERE c.event_id = ?
            ORDER BY c.checkin_time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$eventId]);
    $checkins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $formattedCheckins = array_map(function($checkin) {
        return [
            'checkin_id' => $checkin['checkin_id'],
            'participant_name' => htmlspecialchars($checkin['participant_name']),
            'participant_email' => htmlspecialchars($checkin['participant_email']),
            'checkin_time' => $checkin['checkin_time'],
            'status' => $checkin['status'],
            'registration_id' => $checkin['registration_id']
        ];
    }, $checkins);

    echo json_encode([
        'success' => true,
        'checkins' => $formattedCheckins
    ]);

} catch (Exception $e) {
    error_log("Error in getRecentCheckins: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 