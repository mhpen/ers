<?php
require_once __DIR__ . '/../config/config.php';

try {
    // First check if admin role already exists
    $checkStmt = $conn->prepare("SELECT id FROM admin_roles WHERE name = ?");
    $checkStmt->execute(['Super Admin']);
    $existingRole = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingRole) {
        // Create admin role if it doesn't exist
        $stmt = $conn->prepare("INSERT INTO admin_roles (name, description) VALUES (?, ?)");
        $stmt->execute(['Super Admin', 'Has full access to all system features']);
        $roleId = $conn->lastInsertId();
        echo "Admin role created successfully!\n";
    } else {
        $roleId = $existingRole['id'];
        echo "Admin role already exists.\n";
    }

    // Check if default admin already exists
    $checkAdminStmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $checkAdminStmt->execute(['admin@example.com']);
    $existingAdmin = $checkAdminStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingAdmin) {
        // Create default admin
        $username = 'admin';
        $email = 'admin@example.com';
        $password = 'test@d1n123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $roleId]);
        echo "Default admin created successfully!\n";
        echo "Email: admin@example.com\n";
        echo "Password: test@d1n123\n";
    } else {
        echo "Default admin already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 