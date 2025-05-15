<?php
require_once '../../config/config.php';
require_once '../../models/User.php';

class ClientAuthController {
    private $conn;
    private $user;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            try {
                $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email = ?");
                $stmt->execute([$email]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($client && password_verify($password, $client['password'])) {
                    // Check if account is approved
                    if (!$client['approved']) {
                        // Redirect back to login with pending message
                        header('Location: ../../views/client/clientLogin.php?pending=1');
                        exit();
                    }
                    
                    session_start();
                    $_SESSION['client'] = $client;
                    header('Location: ../../views/client/dashboard.php');
                    exit();
                } else {
                    header('Location: ../../views/client/clientLogin.php?error=1');
                    exit();
                }
            } catch (PDOException $e) {
                header('Location: ../../views/client/clientLogin.php?error=2');
                exit();
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $organization = $_POST['organization'];

            try {
                $stmt = $this->conn->prepare("INSERT INTO clients (name, email, password, organization, approved) VALUES (?, ?, ?, ?, false)");
                $stmt->execute([$name, $email, $password, $organization]);
                
                // Create notification for admin
                $clientId = $this->conn->lastInsertId();
                $this->createAdminNotification($clientId, $name, $organization);
                
                // Fix: Update the redirect path
                header('Location: ../../views/client/registrationPending.php');
                exit();
            } catch (PDOException $e) {
                // Fix: Update the error redirect path
                header('Location: ../../views/client/register.php?error=1');
                exit();
            }
        }
    }

    private function createAdminNotification($clientId, $clientName, $organization) {
        try {
            $message = "New client registration: $clientName from $organization requires approval.";
            $stmt = $this->conn->prepare("INSERT INTO notifications (recipient_type, recipient_id, message) SELECT 'admin', id, ? FROM admins");
            $stmt->execute([$message]);
        } catch (PDOException $e) {
            // Log error but don't interrupt the flow
            error_log("Failed to create admin notification: " . $e->getMessage());
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../clientLogin.php');
        exit();
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new ClientAuthController($conn);
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $authController->register();
    } else {
        $authController->login();
    }
}
