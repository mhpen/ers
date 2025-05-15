<?php
class SessionHelper {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin($role = null) {
        self::init();
        
        if ($role === 'admin') {
            if (!isset($_SESSION['admin'])) {
                $_SESSION['error'] = "Please log in as admin to access this page.";
                header("Location: /event-registration-system/views/admin/adminLogin.php");
                exit();
            }
        } elseif ($role === 'client') {
            if (!isset($_SESSION['client']) || !isset($_SESSION['client']['id'])) {
                $_SESSION['error'] = "Please log in to access this page.";
                header("Location: /event-registration-system/views/auth/clientLogin.php");
                exit();
            }
        } elseif ($role === 'participant') {
            if (!isset($_SESSION['participant']) || !isset($_SESSION['participant']['id'])) {
                $_SESSION['error'] = "Please log in to access this page.";
                header("Location: /event-registration-system/views/auth/participantLogin.php");
                exit();
            }
        } else {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = "Please log in to access this page.";
                header("Location: /event-registration-system/views/auth/login.php");
                exit();
            }
        }
    }

    public static function isLoggedIn($type = 'client') {
        self::init();
        if ($type === 'admin') {
            return isset($_SESSION['admin']);
        } elseif ($type === 'client') {
            return isset($_SESSION['client']) && isset($_SESSION['client']['id']);
        } elseif ($type === 'participant') {
            return isset($_SESSION['participant']) && isset($_SESSION['participant']['id']);
        }
        return isset($_SESSION['user_id']);
    }

    public static function getUserId($type = 'client') {
        self::init();
        if ($type === 'client') {
            return $_SESSION['client']['id'] ?? null;
        } elseif ($type === 'participant') {
            return $_SESSION['participant']['id'] ?? null;
        }
        return $_SESSION['user_id'] ?? null;
    }

    public static function getRole() {
        self::init();
        if (isset($_SESSION['admin'])) {
            return 'admin';
        } elseif (isset($_SESSION['client'])) {
            return 'client';
        } elseif (isset($_SESSION['participant'])) {
            return 'participant';
        }
        return null;
    }

    public static function destroy() {
        self::init();
        session_destroy();
    }

    public static function setFlashMessage($type, $message) {
        self::init();
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public static function getFlashMessage() {
        self::init();
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }

    // Helper method to get client data
    public static function getClientData() {
        self::init();
        return $_SESSION['client'] ?? null;
    }

    // Helper method to get participant data
    public static function getParticipantData() {
        self::init();
        return $_SESSION['participant'] ?? null;
    }
} 