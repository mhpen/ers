<?php
require_once dirname(__FILE__) . '/../config/config.php';

try {
    // Hash the password
    $password = 'test@d1n123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Debug connection
    if (!$conn) {
        die("Database connection failed");
    }

    // Update the admin password
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ?");
    $result = $stmt->execute([$hashedPassword, 'admin']);

    if ($result) {
        echo "Password reset successful!\n";
        echo "Username: admin\n";
        echo "Password: test@d1n123\n";
    } else {
        echo "Password reset failed!\n";
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>