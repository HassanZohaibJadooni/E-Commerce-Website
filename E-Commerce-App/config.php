<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_system";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create DB and use it
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");

    // Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_name VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(100),
        role VARCHAR(20) DEFAULT 'user'
    )");

    // Products table
    $conn->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        price DECIMAL(10,2),
        image VARCHAR(180),
        description TEXT
    )");

    // Carts table
    $conn->exec("CREATE TABLE IF NOT EXISTS carts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        quantity INT DEFAULT 1
    )");

    // Orders table
    $conn->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Default admin user
    $checkAdmin = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $adminEmail = 'admin@gmail.com';
    $checkAdmin->bindParam(':email', $adminEmail);
    $checkAdmin->execute();
    if ($checkAdmin->rowCount() == 0) {
        $conn->exec("INSERT INTO users (user_name, email, password, role)
                     VALUES ('Admin', 'admin@gmail.com', '12345678', 'admin')");
    }

    // Default user user
    $checkEmployee = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $employeeEmail = 'employee@gmail.com';
    $checkEmployee->bindParam(':email', $employeeEmail);
    $checkEmployee->execute();
    if ($checkEmployee->rowCount() == 0) {
        $conn->exec("INSERT INTO users (user_name, email, password, role)
                     VALUES ('Employee', 'employee@gmail.com', '12345678', 'employee')");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
