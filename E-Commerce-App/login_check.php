<?php
require "config.php";

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // agr yeh email hoga or pasword say match karegah
    if ($user && $password === $user['password']) {
        // login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            echo "admin";
        } else {
            echo "user";
        }
    } else {
        echo "invalid";
    }

} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
