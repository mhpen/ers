<?php
class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getDashboardStats() {
        try {
            $stats = [];

            // Get pending client approvals
            $sql = "SELECT COUNT(*) as pending_approvals FROM clients WHERE approved = 0";
            $stmt = $this->conn->query($sql);
            $stats['pending_approvals'] = $stmt->fetch()['pending_approvals'];

            // Get total events
            $sql = "SELECT COUNT(*) as total_events FROM events";
            $stmt = $this->conn->query($sql);
            $stats['total_events'] = $stmt->fetch()['total_events'];

            // Get pending event approvals
            $sql = "SELECT COUNT(*) as pending_events FROM events WHERE status = 'pending'";
            $stmt = $this->conn->query($sql);
            $stats['pending_events'] = $stmt->fetch()['pending_events'];

            // Get active users (clients + participants)
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM clients WHERE approved = 1) +
                    (SELECT COUNT(*) FROM participants WHERE status = 'active') 
                    as active_users";
            $stmt = $this->conn->query($sql);
            $stats['active_users'] = $stmt->fetch()['active_users'];

            // Get recent events
            $sql = "SELECT 
                    e.id,
                    e.title,
                    e.event_date,
                    e.status,
                    c.name as client_name,
                    et.name as event_type
                    FROM events e
                    LEFT JOIN clients c ON e.client_id = c.id
                    LEFT JOIN event_types et ON e.type_id = et.id
                    ORDER BY e.created_at DESC
                    LIMIT 5";
            $stmt = $this->conn->query($sql);
            $stats['recent_events'] = $stmt->fetchAll();

            // Get recent clients
            $sql = "SELECT 
                    id,
                    name,
                    email,
                    organization,
                    approved,
                    created_at
                    FROM clients
                    ORDER BY created_at DESC
                    LIMIT 5";
            $stmt = $this->conn->query($sql);
            $stats['recent_clients'] = $stmt->fetchAll();

            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            return null;
        }
    }

    public function getAnalytics() {
        try {
            $analytics = [];

            // Get registration trends (last 6 months)
            $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count
                    FROM participants
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month ASC";
            $stmt = $this->conn->query($sql);
            $analytics['registration_trends'] = $stmt->fetchAll();

            // Get popular event types
            $sql = "SELECT 
                    et.name as type_name,
                    COUNT(e.id) as event_count
                    FROM event_types et
                    LEFT JOIN events e ON et.id = e.type_id
                    GROUP BY et.id
                    ORDER BY event_count DESC
                    LIMIT 5";
            $stmt = $this->conn->query($sql);
            $analytics['popular_event_types'] = $stmt->fetchAll();

            // Get monthly statistics
            $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    (SELECT COUNT(*) FROM participants WHERE DATE_FORMAT(created_at, '%Y-%m') = month) as new_users,
                    COUNT(*) as events_created
                    FROM events
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month DESC";
            $stmt = $this->conn->query($sql);
            $analytics['monthly_stats'] = $stmt->fetchAll();

            return $analytics;
        } catch (PDOException $e) {
            error_log("Error getting analytics: " . $e->getMessage());
            return null;
        }
    }
} 