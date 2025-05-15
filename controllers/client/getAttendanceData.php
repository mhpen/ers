<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    $clientId = $_SESSION['client']['id'];

    // Get overall statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT e.id) as total_events,
            COUNT(DISTINCT r.id) as total_registrations,
            COUNT(DISTINCT c.checkin_id) as total_checkins
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        LEFT JOIN checkins c ON e.id = c.event_id
        WHERE e.client_id = ?
    ");
    $stmt->execute([$clientId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate average attendance rate
    $stmt = $conn->prepare("
        SELECT 
            e.id,
            COUNT(DISTINCT r.id) as registrations,
            COUNT(DISTINCT c.checkin_id) as checkins
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        LEFT JOIN checkins c ON e.id = c.event_id
        WHERE e.client_id = ?
        GROUP BY e.id
    ");
    $stmt->execute([$clientId]);
    $eventStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalRate = 0;
    $eventCount = count($eventStats);
    foreach ($eventStats as $event) {
        if ($event['registrations'] > 0) {
            $totalRate += ($event['checkins'] / $event['registrations']) * 100;
        }
    }
    $averageAttendanceRate = $eventCount > 0 ? round($totalRate / $eventCount) : 0;

    // Get events list with attendance data
    $stmt = $conn->prepare("
        SELECT 
            e.*,
            COUNT(DISTINCT r.id) as registrations,
            COUNT(DISTINCT c.checkin_id) as checkins,
            CASE 
                WHEN COUNT(DISTINCT r.id) > 0 
                THEN ROUND((COUNT(DISTINCT c.checkin_id) / COUNT(DISTINCT r.id)) * 100)
                ELSE 0
            END as attendance_rate
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        LEFT JOIN checkins c ON e.id = c.event_id
        WHERE e.client_id = ?
        GROUP BY e.id
        ORDER BY e.event_date DESC
    ");
    $stmt->execute([$clientId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'totalEvents' => $stats['total_events'],
        'totalRegistrations' => $stats['total_registrations'],
        'totalCheckins' => $stats['total_checkins'],
        'averageAttendanceRate' => $averageAttendanceRate,
        'events' => $events
    ]);

} catch (Exception $e) {
    error_log("Error in getAttendanceData: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch attendance data',
        'message' => $e->getMessage()
    ]);
} 