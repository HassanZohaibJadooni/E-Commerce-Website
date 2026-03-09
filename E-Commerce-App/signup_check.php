<?php
require "config.php";

// Variables
$user_name = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$cpassword = $_POST['cpassword'] ?? '';

try {
    // Validate basic fields
    if (empty($user_name) || empty($email) || empty($password) || empty($cpassword)) {
        echo "error: missing fields";
        exit;
    }

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    // If email exists
    if ($stmt->rowCount() > 0) {
        echo "exists";
    } elseif ($password !== $cpassword) { // if password and cpassword is not match
        echo "mismatch";
    } elseif (strlen($password) < 8) {
        echo "short";
    } else {
        // Insert password
        $insert = $conn->prepare("INSERT INTO users (user_name, email, password) VALUES (:user_name, :email, :password)");
        $insert->bindParam(":user_name", $user_name);
        $insert->bindParam(":email", $email);
        $insert->bindParam(":password", $password);
        $insert->execute();
        echo "success";
    }
} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
?>
