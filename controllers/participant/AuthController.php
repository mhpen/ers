<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/User.php';
require_once '../../models/Participant.php';

class ParticipantAuthController {
    private $conn;
    private $user;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                header('Location: ../../views/participant/participantLogin.php?error=1');
                exit();
            }

            $participant = new Participant($this->conn);
            $result = $participant->login($email, $password);

            if ($result) {
                $_SESSION['participant'] = $result;
                $_SESSION['participant_id'] = $result['id'];
                header('Location: ../../views/participant/participantPage.php');
                exit();
            } else {
                header('Location: ../../views/participant/participantLogin.php?error=1');
                exit();
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            header('Location: ../../views/participant/participantLogin.php?error=2');
            exit();
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $participant = new Participant($this->conn);
            $result = $participant->register($name, $email, $password);

            if ($result['success']) {
                header('Location: ../../views/participant/participantLogin.php?registration=success');
                exit();
            } else {
                header('Location: ../../views/participant/register.php?error=1&message=' . urlencode($result['message']));
                exit();
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../../views/participant/participantLogin.php');
        exit();
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new ParticipantAuthController($conn);
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $authController->register();
    } else {
        $authController->login();
    }
} else if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authController = new ParticipantAuthController($conn);
    $authController->logout();
}
