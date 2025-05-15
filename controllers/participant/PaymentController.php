<?php
require_once '../../config/config.php';
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('participant');

class PaymentController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function createRegistration($participant_id, $event_id, $data) {
        // Start transaction
        $this->conn->beginTransaction();

        try {
            // Get registration code from POST or generate new one
            $registration_code = $_POST['registration_code'] ?? 'REG-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Create registration
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
            
            $regStmt = $this->conn->prepare($regSql);
            $regStmt->execute([
                $participant_id, 
                $event_id,
                $data['contact_number'],
                $data['emergency_contact'],
                $data['emergency_number'],
                $data['notes'],
                $registration_code
            ]);
            
            $registration_id = $this->conn->lastInsertId();

            // Create QR code
            $qr_code = 'REG-' . str_pad($registration_id, 8, '0', STR_PAD_LEFT);
            $updateQrSql = "UPDATE registrations SET qr_code = ? WHERE id = ?";
            $updateQrStmt = $this->conn->prepare($updateQrSql);
            $updateQrStmt->execute([$qr_code, $registration_id]);

            $this->conn->commit();
            return $registration_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function processPayment() {
        try {
            error_log("Starting payment process...");
            error_log("POST data: " . print_r($_POST, true));
            error_log("SESSION data: " . print_r($_SESSION, true));
            error_log("FILES data: " . print_r($_FILES, true));

            $participant_id = $_SESSION['participant_id'];
            $event_id = $_POST['event_id'];
            $registration_data = $_SESSION['registration_data'] ?? null;

            if (!$registration_data) {
                throw new Exception("Registration data not found in session");
            }

            // Get event details for price
            $eventSql = "SELECT price FROM events WHERE id = ?";
            $eventStmt = $this->conn->prepare($eventSql);
            $eventStmt->execute([$event_id]);
            $event = $eventStmt->fetch();

            if (!$event) {
                throw new Exception("Event not found");
            }

            error_log("Creating registration...");
            // First create the registration
            $registration_id = $this->createRegistration($participant_id, $event_id, $registration_data);
            error_log("Registration created with ID: " . $registration_id);

            error_log("Uploading payment proof...");
            // Handle file upload
            $proof_path = $this->uploadPaymentProof($_FILES['payment_proof']);
            error_log("Payment proof uploaded: " . $proof_path);

            // Start transaction for payment
            $this->conn->beginTransaction();

            try {
                error_log("Creating payment record...");
                // Create payment record
                $sql = "INSERT INTO payments (registration_id, participant_id, event_id, amount, 
                        payment_method, reference_number, proof_file, status, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)";
                
                $stmt = $this->conn->prepare($sql);
                $result = $stmt->execute([
                    $registration_id,
                    $participant_id,
                    $event_id,
                    $event['price'],
                    $_POST['payment_method'],
                    $_POST['reference_number'],
                    $proof_path
                ]);

                if (!$result) {
                    throw new Exception("Failed to insert payment record: " . implode(", ", $stmt->errorInfo()));
                }

                $this->conn->commit();
                error_log("Payment record created successfully");

                // Clear session data
                unset($_SESSION['registration_data']);

                // Send confirmation email
                $this->sendConfirmationEmail($registration_id);

                // Redirect to confirmation page
                $_SESSION['success'] = "Registration and payment submitted successfully!";
                header("Location: ../../views/participant/registration-confirmation.php?id=" . $registration_id);
                exit();

            } catch (Exception $e) {
                $this->conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Payment error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = "Payment submission failed: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    private function uploadPaymentProof($file) {
        try {
            // Create uploads directory if it doesn't exist
            $upload_dir = "../../uploads/payment_proofs/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate unique filename
            $filename = uniqid() . '_' . basename($file['name']);
            $target_path = $upload_dir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return $filename;
            } else {
                throw new Exception("Failed to upload file.");
            }
        } catch (Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            throw $e;
        }
    }

    private function sendConfirmationEmail($registration_id) {
        // TODO: Implement email sending logic
        // For now, just log that we would send an email
        error_log("Would send confirmation email for registration ID: " . $registration_id);
    }
}

// Handle payment request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    $controller = new PaymentController($conn);
    $controller->processPayment();
}