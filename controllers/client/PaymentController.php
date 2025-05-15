<?php
require_once '../../config/config.php';
require_once '../../helpers/EmailHelper.php';
require_once '../../helpers/NotificationHelper.php';

class PaymentController {
    private $conn;
    private $emailHelper;
    private $notificationHelper;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->emailHelper = new EmailHelper();
        $this->notificationHelper = new NotificationHelper();
    }

    public function verifyPayment($paymentId, $status, $remarks = '') {
        try {
            error_log("=== Starting Payment Verification ===");
            error_log("Payment ID: " . $paymentId);
            error_log("Status: " . $status);

            // First verify the payment exists
            $checkSql = "SELECT * FROM payments WHERE id = ?";
            $stmt = $this->conn->prepare($checkSql);
            $stmt->execute([$paymentId]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                throw new Exception("Payment not found with ID: " . $paymentId);
            }

            $this->conn->beginTransaction();

            // Update payment status
            $updateSql = "UPDATE payments SET 
                         status = ?, 
                         decline_notes = ?, 
                         verified_at = NOW(),
                         verified_by = ?
                         WHERE id = ?";
            $stmt = $this->conn->prepare($updateSql);
            $result = $stmt->execute([
                $status,
                $status === 'declined' ? $remarks : null,
                $_SESSION['client']['id'], // Add verified_by
                $paymentId
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update payment status");
            }

            // Get registration details
            $regSql = "SELECT r.id as registration_id, r.participant_id, e.title as event_title 
                      FROM payments p
                      JOIN registrations r ON p.registration_id = r.id
                      JOIN events e ON r.event_id = e.id
                      WHERE p.id = ?";
            
            $stmt = $this->conn->prepare($regSql);
            $stmt->execute([$paymentId]);
            $registration = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registration) {
                throw new Exception("Registration details not found");
            }

            if ($status === 'confirmed') {
                // Update registration status
                $updateRegSql = "UPDATE registrations SET status = 'confirmed' WHERE id = ?";
                $stmt = $this->conn->prepare($updateRegSql);
                $result = $stmt->execute([$registration['registration_id']]);
                
                if (!$result) {
                    throw new Exception("Failed to update registration status");
                }

                // Create notification
                try {
                    $notificationData = [
                        'recipient_type' => 'participant',
                        'recipient_id' => $registration['participant_id'],
                        'message' => "Your payment for {$registration['event_title']} has been confirmed.",
                        'is_read' => 0
                    ];
                    $this->notificationHelper->createNotification($notificationData);
                } catch (Exception $e) {
                    error_log("Notification error: " . $e->getMessage());
                    // Continue even if notification fails
                }
            } else {
                // Create decline notification
                try {
                    $notificationData = [
                        'recipient_type' => 'participant',
                        'recipient_id' => $registration['participant_id'],
                        'message' => "Your payment for {$registration['event_title']} was declined. Reason: {$remarks}",
                        'is_read' => 0
                    ];
                    $this->notificationHelper->createNotification($notificationData);
                } catch (Exception $e) {
                    error_log("Notification error: " . $e->getMessage());
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Payment verification failed: " . $e->getMessage());
            return false;
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    if (isset($_POST['action']) && $_POST['action'] === 'verify_payment') {
        if (!isset($_POST['payment_id']) || !isset($_POST['status'])) {
            $_SESSION['error'] = "Missing required fields";
            header('Location: ../../views/client/payments.php');
            exit();
        }

        $controller = new PaymentController();
        $result = $controller->verifyPayment(
            $_POST['payment_id'],
            $_POST['status'],
            $_POST['remarks'] ?? ''
        );

        if ($result) {
            $_SESSION['success'] = "Payment has been " . ($_POST['status'] === 'confirmed' ? 'verified' : 'declined');
        } else {
            $_SESSION['error'] = "Failed to process payment verification. Please check error logs.";
        }
        
        header('Location: ../../views/client/payments.php');
        exit();
    }
}
