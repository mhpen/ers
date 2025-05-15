<?php
session_start();

class AuthController {
    public static function logout() {
        // Clear all session data
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to main login page with correct path
        header('Location: /event-registration-system/views/client/clientLogin.php');
        exit();
    }

    public function clientLogin() {
        try {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $sql = "SELECT * FROM clients WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $client = $stmt->fetch();

            if ($client && password_verify($password, $client['password'])) {
                // Store client data in session
                $_SESSION['client'] = [
                    'id' => $client['id'],
                    'name' => $client['name'],
                    'email' => $client['email']
                ];
                
                header("Location: ../views/client/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password";
                header("Location: ../views/auth/clientLogin.php");
                exit();
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['error'] = "Login failed. Please try again.";
            header("Location: ../views/auth/clientLogin.php");
            exit();
        }
    }
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    AuthController::logout();
} 