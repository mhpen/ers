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

    error_log("Fetching event details for event ID: $eventId and client ID: $clientId");

    // Get event details
    $stmt = $conn->prepare("
        SELECT e.*, 
               COUNT(DISTINCT r.id) as total_registrations,
               COUNT(DISTINCT c.checkin_id) as total_checkins
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        LEFT JOIN checkins c ON e.id = c.event_id
        WHERE e.id = ? AND e.client_id = ?
        GROUP BY e.id, e.title, e.event_date, e.location, e.client_id
    ");
    
    error_log("Executing event details query");
    $stmt->execute([$eventId, $clientId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        error_log("No event found for ID: $eventId");
        throw new Exception('Event not found');
    }

    error_log("Event found: " . json_encode($event));

    // Calculate statistics
    $stats = [
        'registration_rate' => '-', // Removed capacity-based calculation
        'checkin_rate' => $event['total_registrations'] ? round(($event['total_checkins'] / $event['total_registrations']) * 100) : 0,
        'total_attendees' => $event['total_checkins']
    ];

    error_log("Calculated stats: " . json_encode($stats));

    // Get attendee list
    $stmt = $conn->prepare("
        SELECT p.name, p.email, c.checkin_time
        FROM checkins c
        JOIN participants p ON c.participant_id = p.id
        WHERE c.event_id = ?
        ORDER BY c.checkin_time DESC
    ");
    
    error_log("Executing attendee list query");
    $stmt->execute([$eventId]);
    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Found " . count($attendees) . " attendees");

    $response = [
        'event' => $event,
        'stats' => $stats,
        'attendees' => $attendees
    ];

    error_log("Sending response: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in getEventDetails: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'details' => 'Check server logs for more information'
    ]);
} 