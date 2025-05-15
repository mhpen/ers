<?php

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createAdmin($username, $email, $password, $roleId) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO admins (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword, $roleId]);
    }

    public function createClient($name, $email, $password, $organization) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO clients (name, email, password, organization) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword, $organization]);
    }

    public function createParticipant($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO participants (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword]);
    }
}
