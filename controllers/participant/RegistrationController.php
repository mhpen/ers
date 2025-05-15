<?php
require_once '../../config/config.php';
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');

class RegistrationController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register() {
        try {
            // Debug logging
            error_log("[DEBUG] Full POST data: " . print_r($_POST, true));
            error_log("[DEBUG] Debug code received: " . ($_POST['debug_code'] ?? 'not set'));
            
            $participant_id = $_SESSION['participant_id'];
            $event_id = $_POST['event_id'];
            $registration_code = $_POST['registration_code'] ?? null;
            
            error_log("[DEBUG] Registration code from POST: " . ($registration_code ?? 'null'));
            
            if (empty($registration_code)) {
                $registration_code = 'REG-' . strtoupper(uniqid());
                error_log("[DEBUG] Generated new registration code: " . $registration_code);
            }

            // Store registration data
            $_SESSION['registration_data'] = [
                'event_id' => $event_id,
                'participant_id' => $participant_id,
                'contact_number' => $_POST['contact_number'],
                'emergency_contact' => $_POST['emergency_contact'],
                'emergency_number' => $_POST['emergency_number'],
                'notes' => $_POST['notes'] ?? null,
                'terms' => isset($_POST['terms']),
                'registration_code' => $registration_code
            ];
            
            error_log("[DEBUG] Stored registration data in session: " . print_r($_SESSION['registration_data'], true));
            error_log("[DEBUG] Registration code in session data: " . ($_SESSION['registration_data']['registration_code'] ?? 'not set'));

            // Validate required fields
            $required_fields = ['contact_number', 'emergency_contact', 'emergency_number', 'terms'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    $_SESSION['error'] = "Please fill in all required fields.";
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                }
            }

            // Validate contact numbers
            if (!preg_match("/^[0-9]{11}$/", $_POST['contact_number']) || 
                !preg_match("/^[0-9]{11}$/", $_POST['emergency_number'])) {
                $_SESSION['error'] = "Invalid contact number format.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            
            // Check if already registered
            $checkSql = "SELECT id FROM registrations WHERE participant_id = ? AND event_id = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute([$participant_id, $event_id]);
            
            if ($checkStmt->fetch()) {
                $_SESSION['error'] = "You are already registered for this event.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // Get event details for payment
            $eventSql = "SELECT price FROM events WHERE id = ?";
            $eventStmt = $this->conn->prepare($eventSql);
            $eventStmt->execute([$event_id]);
            $event = $eventStmt->fetch();

            if ($event['price'] > 0) {
                // For paid events, redirect to payment page
                $_SESSION['registration_pending'] = true;
                error_log("[DEBUG] Redirecting to payment with registration code: " . $registration_code);
                header("Location: ../../views/participant/payment.php?event_id=" . $event_id);
            } else {
                // For free events, complete registration immediately
                $registration_id = $this->completeRegistration($participant_id, $event_id, $_SESSION['registration_data']);
                if ($registration_id) {
                    unset($_SESSION['registration_data']); // Clear the session data after successful registration
                    header("Location: ../../views/participant/registration-confirmation.php?id=" . $registration_id);
                } else {
                    throw new Exception("Failed to get registration ID");
                }
            }
            exit();

        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    private function completeRegistration($participant_id, $event_id, $data) {
        try {
            error_log("[DEBUG] Starting registration with data: " . print_r($data, true));
            error_log("[DEBUG] Registration code being used: " . ($data['registration_code'] ?? 'not set'));

            $this->conn->beginTransaction();

            // Verify registration code exists
            if (empty($data['registration_code'])) {
                error_log("[DEBUG] No registration code found in data!");
                throw new Exception("Registration code is missing");
            }

            $regSql = "INSERT INTO registrations (
                participant_id, 
                event_id, 
                status, 
                contact_number, 
                emergency_contact, 
                emergency_number, 
                notes, 
                registered_at, 
                registration_code
            ) VALUES (?, ?, 'pending', ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)";
            
            error_log("[DEBUG] About to execute SQL with values: " . print_r([
                $participant_id, 
                $event_id,
                $data['contact_number'],
                $data['emergency_contact'],
                $data['emergency_number'],
                $data['notes'],
                $data['registration_code']
            ], true));

            $regStmt = $this->conn->prepare($regSql);
            $result = $regStmt->execute([
                $participant_id, 
                $event_id,
                $data['contact_number'],
                $data['emergency_contact'],
                $data['emergency_number'],
                $data['notes'],
                $data['registration_code']
            ]);

            if (!$result) {
                error_log("[DEBUG] Database insert failed. Error info: " . print_r($regStmt->errorInfo(), true));
                throw new Exception("Failed to insert registration");
            }

            $registration_id = $this->conn->lastInsertId();
            error_log("[DEBUG] Successfully created registration with ID: " . $registration_id);

            // Create QR code
            $qr_code = 'REG-' . str_pad($registration_id, 8, '0', STR_PAD_LEFT);
            $updateQrSql = "UPDATE registrations SET qr_code = ? WHERE id = ?";
            $updateQrStmt = $this->conn->prepare($updateQrSql);
            $updateQrStmt->execute([$qr_code, $registration_id]);

            $this->conn->commit();
            return $registration_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            throw $e;
        }
    }
}

// Handle registration request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $controller = new RegistrationController($conn);
    $controller->register();
}
