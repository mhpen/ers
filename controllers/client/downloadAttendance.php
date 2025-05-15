<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('client');
require_once '../../config/config.php';

if (!isset($_GET['event_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Event ID is required']);
    exit;
}

$event_id = $_GET['event_id'];
$client_id = $_SESSION['client']['id'];

try {
    // Verify event belongs to client
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND client_id = ?");
    $stmt->execute([$event_id, $client_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    // Get attendance data with contact number from registrations
    $stmt = $conn->prepare("
        SELECT 
            p.name,
            p.email,
            r.contact_number as phone,
            c.checkin_time
        FROM participants p
        INNER JOIN registrations r ON p.id = r.participant_id
        INNER JOIN checkins c ON p.id = c.participant_id
        WHERE c.event_id = ?
        ORDER BY c.checkin_time ASC
    ");

    $stmt->execute([$event_id]);
    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($attendees)) {
        http_response_code(404);
        echo json_encode(['error' => 'No attendance records found']);
        exit;
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="event-attendance-' . $event_id . '.csv"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Write CSV headers with custom labels
    fputcsv($output, [
        'Participant Name',
        'Email Address',
        'Phone Number',
        'Check-in Time'
    ]);

    // Write data rows with formatted date
    foreach ($attendees as $attendee) {
        $checkin_time = new DateTime($attendee['checkin_time']);
        fputcsv($output, [
            $attendee['name'],
            $attendee['email'],
            $attendee['phone'],
            $checkin_time->format('Y-m-d H:i:s')
        ]);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log('CSV Download Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate attendance report']);
    exit;
}