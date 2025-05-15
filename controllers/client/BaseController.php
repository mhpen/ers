<?php
require_once '../../config/config.php';
require_once '../../models/ClientActivityLogger.php';
require_once '../../helpers/SessionHelper.php';

class BaseController {
    protected $conn;
    protected $activityLogger;
    protected $clientId;

    public function __construct() {
        global $conn;
        
        SessionHelper::requireLogin('client');

        $this->conn = $conn;
        $this->clientId = SessionHelper::getUserId('client');
        $this->activityLogger = new ClientActivityLogger($conn);
    }

    protected function redirect($path, $message = '', $type = 'success') {
        $url = $path;
        if ($message) {
            $url .= "?{$type}=" . urlencode($message);
        }
        header("Location: $url");
        exit();
    }
} 