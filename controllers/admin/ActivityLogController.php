<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('admin');

require_once '../../config/config.php';
require_once '../../models/ActivityLogger.php';

class ActivityLogController {
    private $conn;
    private $activityLogger;
    private $itemsPerPage = 10;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->activityLogger = new ActivityLogger($conn);
    }

    public function getFilteredLogs($type = null, $date = null, $sort = 'newest', $page = 1) {
        try {
            // Count total records for pagination
            $countSql = "SELECT COUNT(*) FROM activity_logs al WHERE 1=1";
            $params = [];

            if ($type) {
                $countSql .= " AND al.action LIKE :type";
                $params[':type'] = $type . '%';
            }

            if ($date) {
                $countSql .= " AND DATE(al.created_at) = :date";
                $params[':date'] = $date;
            }

            $countStmt = $this->conn->prepare($countSql);
            foreach ($params as $key => &$value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalRecords = $countStmt->fetchColumn();

            // Calculate pagination
            $totalPages = ceil($totalRecords / $this->itemsPerPage);
            $page = max(1, min($page, $totalPages));
            $offset = ($page - 1) * $this->itemsPerPage;

            // Main query with pagination
            $sql = "SELECT al.*, 
                    CASE 
                        WHEN al.actor_type = 'admin' THEN a.username
                        WHEN al.actor_type = 'client' THEN c.name
                    END as actor_name
                    FROM activity_logs al
                    LEFT JOIN admins a ON al.actor_type = 'admin' AND al.actor_id = a.id
                    LEFT JOIN clients c ON al.actor_type = 'client' AND al.actor_id = c.id
                    WHERE 1=1";
            
            if ($type) {
                $sql .= " AND al.action LIKE :type";
            }

            if ($date) {
                $sql .= " AND DATE(al.created_at) = :date";
            }

            // Add sorting
            $sql .= match($sort) {
                'oldest' => " ORDER BY al.created_at ASC",
                default => " ORDER BY al.created_at DESC"
            };

            $sql .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => &$value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $this->itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'logs' => $logs,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalRecords' => $totalRecords,
                    'itemsPerPage' => $this->itemsPerPage
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error fetching activity logs: " . $e->getMessage());
            return [
                'logs' => [],
                'pagination' => [
                    'currentPage' => 1,
                    'totalPages' => 1,
                    'totalRecords' => 0,
                    'itemsPerPage' => $this->itemsPerPage
                ]
            ];
        }
    }

    public function getActivityTypes() {
        try {
            $sql = "SELECT DISTINCT SUBSTRING_INDEX(action, '_', 1) as type 
                    FROM activity_logs 
                    ORDER BY type";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching activity types: " . $e->getMessage());
            return [];
        }
    }
} 