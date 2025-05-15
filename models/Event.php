<?php
class Event {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO events (
                client_id, title, description, category_id, type_id,
                event_date, registration_deadline, location, price,
                slots, max_participants_per_registration, visibility, 
                status, banner, created_at, updated_at
            ) VALUES (
                :client_id, :title, :description, :category_id, :type_id,
                :event_date, :registration_deadline, :location, :price,
                :slots, :max_participants_per_registration, :visibility,
                :status, :banner, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )";

            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                ':client_id' => $data['client_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':category_id' => $data['category_id'],
                ':type_id' => $data['type_id'],
                ':event_date' => $data['event_date'],
                ':registration_deadline' => $data['registration_deadline'],
                ':location' => $data['location'],
                ':price' => $data['price'],
                ':slots' => $data['slots'],
                ':max_participants_per_registration' => $data['max_participants_per_registration'],
                ':visibility' => $data['visibility'],
                ':status' => $data['status'],
                ':banner' => $data['banner']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating event: " . $e->getMessage());
            return false;
        }
    }

    public function getClientEvents($clientId) {
        try {
            $sql = "SELECT 
                    e.*,
                    COUNT(r.id) as registrations_count
                FROM events e
                LEFT JOIN registrations r ON e.id = r.event_id
                WHERE e.client_id = :client_id
                GROUP BY e.id
                ORDER BY e.event_date DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':client_id' => $clientId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching client events: " . $e->getMessage());
            return [];
        }
    }

    public function getById($eventId) {
        try {
            $sql = "SELECT e.*, 
                    c.name as category_name, 
                    t.name as type_name,
                    COUNT(r.id) as registrations_count
                FROM events e
                LEFT JOIN categories c ON e.category_id = c.id
                LEFT JOIN event_types t ON e.type_id = t.id
                LEFT JOIN registrations r ON e.id = r.event_id
                WHERE e.id = :id
                GROUP BY e.id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $eventId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event: " . $e->getMessage());
            return false;
        }
    }

    public function update($eventId, $data) {
        try {
            $sql = "UPDATE events SET 
                    title = :title,
                    description = :description,
                    category_id = :category_id,
                    type_id = :type_id,
                    event_date = :event_date,
                    registration_deadline = :registration_deadline,
                    location = :location,
                    price = :price,
                    slots = :slots,
                    max_participants_per_registration = :max_participants_per_registration,
                    visibility = :visibility,
                    banner = :banner,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id AND client_id = :client_id";

            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute(array_merge($data, [':id' => $eventId]));
        } catch (PDOException $e) {
            error_log("Error updating event: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($eventId, $status) {
        try {
            $sql = "UPDATE events SET 
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':id' => $eventId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating event status: " . $e->getMessage());
            return false;
        }
    }

    public function delete($eventId, $clientId) {
        try {
            $sql = "DELETE FROM events WHERE id = :id AND client_id = :client_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id' => $eventId,
                ':client_id' => $clientId
            ]);
        } catch (PDOException $e) {
            error_log("Error deleting event: " . $e->getMessage());
            return false;
        }
    }

    public function getFilteredEvents($status = 'all', $category = 'all', $type = 'all', $sort = 'newest') {
        try {
            $sql = "SELECT e.*, 
                    c.name as category_name, 
                    t.name as type_name,
                    cl.name as client_name
                    FROM events e
                    LEFT JOIN categories c ON e.category_id = c.id
                    LEFT JOIN event_types t ON e.type_id = t.id
                    LEFT JOIN clients cl ON e.client_id = cl.id
                    WHERE 1=1";
            
            $params = [];

            if ($status !== 'all') {
                $sql .= " AND e.status = :status";
                $params[':status'] = $status;
            }

            if ($category !== 'all') {
                $sql .= " AND e.category_id = :category_id";
                $params[':category_id'] = $category;
            }

            if ($type !== 'all') {
                $sql .= " AND e.type_id = :type_id";
                $params[':type_id'] = $type;
            }

            // Add sorting
            $sql .= match($sort) {
                'oldest' => " ORDER BY e.created_at ASC",
                'title' => " ORDER BY e.title ASC",
                'date' => " ORDER BY e.event_date ASC",
                default => " ORDER BY e.created_at DESC"
            };

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching filtered events: " . $e->getMessage());
            return [];
        }
    }

    public function getCategories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public function getEventTypes() {
        try {
            $sql = "SELECT * FROM event_types ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event types: " . $e->getMessage());
            return [];
        }
    }
}
