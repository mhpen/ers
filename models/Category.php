<?php
class Category {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllCategories() {
        try {
            $sql = "SELECT c.*, COUNT(e.id) as event_count 
                    FROM categories c 
                    LEFT JOIN events e ON c.id = e.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public function create($name, $description) {
        try {
            // Ensure description is not empty
            $description = trim($description) ?: "Events in the {$name} category";
            
            $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $name,
                ':description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name, $description) {
        try {
            // Ensure description is not empty
            $description = trim($description) ?: "Events in the {$name} category";
            
            $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return null;
        }
    }
}
