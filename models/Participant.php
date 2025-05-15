<?php
class Participant {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($email, $password) {
        try {
            error_log("Attempting login for email: " . $email);
            
            $sql = "SELECT * FROM participants WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':email' => $email]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                error_log("No participant found with email: " . $email);
                return false;
            }

            if (password_verify($password, $participant['password'])) {
                error_log("Password verified successfully for: " . $email);
                unset($participant['password']); // Remove password from session data
                return $participant;
            } else {
                error_log("Invalid password for: " . $email);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function register($name, $email, $password) {
        try {
            // Check if email already exists
            $checkSql = "SELECT COUNT(*) FROM participants WHERE email = :email";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute([':email' => $email]);
            
            if ($checkStmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Insert new participant
            $sql = "INSERT INTO participants (name, email, password, created_at) 
                    VALUES (:name, :email, :password, CURRENT_TIMESTAMP)";
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            return [
                'success' => $success,
                'message' => $success ? 'Registration successful' : 'Registration failed'
            ];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM participants WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($participant) {
                unset($participant['password']);
            }
            
            return $participant;
        } catch (PDOException $e) {
            error_log("Error fetching participant: " . $e->getMessage());
            return null;
        }
    }

    public function update($id, $data) {
        try {
            $updateFields = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                if ($key !== 'id' && $key !== 'password') {
                    $updateFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (!empty($updateFields)) {
                $sql = "UPDATE participants SET " . implode(', ', $updateFields) . 
                       ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
                
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute($params);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error updating participant: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $sql = "SELECT password FROM participants WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant || !password_verify($currentPassword, $participant['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }

            // Update to new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE participants SET password = :password, 
                         updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            
            $updateStmt = $this->conn->prepare($updateSql);
            $success = $updateStmt->execute([
                ':password' => $hashedPassword,
                ':id' => $id
            ]);

            return [
                'success' => $success,
                'message' => $success ? 'Password updated successfully' : 'Failed to update password'
            ];
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update password'];
        }
    }

    public function getFilteredParticipants($search = '', $status = 'all', $sort = 'newest') {
        $sql = "SELECT * FROM participants WHERE 1=1";
        $params = [];

        // Add search condition
        if ($search) {
            $sql .= " AND (name LIKE ? OR email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Add status condition
        if ($status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        // Add sorting
        $sql .= match ($sort) {
            'oldest' => " ORDER BY created_at ASC",
            'name' => " ORDER BY name ASC",
            'email' => " ORDER BY email ASC",
            default => " ORDER BY created_at DESC"
        };

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function toggleStatus($id) {
        $sql = "UPDATE participants SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
} 