<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/User.php';

class AdminAuthController {
    private $conn;
    private $user;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            try {
                // Debug database connection
                error_log("Database connection status: " . ($this->conn ? "Connected" : "Not connected"));
                
                $stmt = $this->conn->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                // Debug admin data
                error_log("Admin data: " . print_r($admin, true));

                if ($admin) {
                    // Debug password match
                    error_log("Submitted password: " . $password);
                    error_log("Stored hashed password: " . $admin['password']);

                    if (password_verify($password, $admin['password'])) {
                        $_SESSION['admin'] = $admin;
                        header('Location: ../../views/admin/dashboard.php');
                        exit();
                    } else {
                        error_log("Password verification failed");
                        header('Location: ../../views/admin/adminLogin.php?error=1');
                        exit();
                    }
                } else {
                    error_log("No admin found with username: " . $username);
                    header('Location: ../../views/admin/adminLogin.php?error=1');
                    exit();
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                header('Location: ../../views/admin/adminLogin.php?error=2');
                exit();
            }
        }
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        header('Location: ../../views/admin/adminLogin.php');
        exit();
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AdminAuthController($conn);
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $authController->login();
    }
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authController = new AdminAuthController($conn);
    $authController->logout();
}
