<?php
require_once '../config/config.php';
require_once '../helpers/SessionHelper.php';

class PaymentController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function verifyPayment($payment_id, $status, $decline_notes = null) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Get payment and registration details
            $sql = "SELECT p.*, r.id as registration_id, e.client_id 
                    FROM payments p 
                    JOIN registrations r ON p.registration_id = r.id 
                    JOIN events e ON r.event_id = e.id 
                    WHERE p.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$payment_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                throw new Exception("Payment not found");
            }

            // Debug log
            error_log("Processing payment ID: " . $payment_id . " with status: " . $status);

            // Update payment status
            $updatePaymentSql = "UPDATE payments 
                                SET status = :status, 
                                    verified_at = CURRENT_TIMESTAMP,
                                    decline_notes = :notes
                                WHERE id = :id";
            $stmt = $this->conn->prepare($updatePaymentSql);
            $result1 = $stmt->execute([
                ':status' => $status,
                ':notes' => $decline_notes,
                ':id' => $payment_id
            ]);

            // Debug log
            error_log("Payment update result: " . ($result1 ? 'success' : 'failed'));

            // Update registration status
            $regStatus = ($status === 'accepted') ? 'confirmed' : 'declined';
            $updateRegSql = "UPDATE registrations 
                            SET status = :status 
                            WHERE id = :id";
            $stmt = $this->conn->prepare($updateRegSql);
            $result2 = $stmt->execute([
                ':status' => $regStatus,
                ':id' => $payment['registration_id']
            ]);

            // Debug log
            error_log("Registration update result: " . ($result2 ? 'success' : 'failed'));

            if (!$result1 || !$result2) {
                throw new Exception("Failed to update status");
            }

            $this->conn->commit();
            $_SESSION['success'] = $status === 'accepted' 
                ? "Payment accepted successfully!" 
                : "Payment declined successfully.";
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Payment verification error: " . $e->getMessage());
            $_SESSION['error'] = "Failed to process payment. Error: " . $e->getMessage();
            return false;
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        SessionHelper::requireLogin('client');
        
        $action = $_POST['action'] ?? '';
        $payment_id = $_POST['payment_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $decline_notes = $_POST['decline_notes'] ?? null;

        if (!$payment_id || !$status) {
            throw new Exception("Payment ID and status are required");
        }

        $controller = new PaymentController($conn);

        if ($action === 'verify_payment') {
            if ($status === 'declined' && empty($decline_notes)) {
                throw new Exception("Please provide a reason for declining the payment");
            }

            $result = $controller->verifyPayment($payment_id, $status, $decline_notes);
            if (!$result) {
                throw new Exception("Verification failed");
            }
        }

    } catch (Exception $e) {
        error_log("Payment verification error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to verify payment: " . $e->getMessage();
    }

    header('Location: ../views/client/payments.php');
    exit();
} 