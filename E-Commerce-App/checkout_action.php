<?php
require "config.php";
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM carts WHERE user_id=?");
$stmt->execute([$user_id]);
$carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($carts)) {
    echo json_encode(["success" => false, "message" => "Your cart is empty!"]);
    exit;
}

try {
    foreach ($carts as $c) {
        $pid = $c['product_id'];
        $qty = $c['quantity'];
        $pstmt = $conn->prepare("SELECT price FROM products WHERE id=?");
        $pstmt->execute([$pid]);
        $p = $pstmt->fetch(PDO::FETCH_ASSOC);
        if ($p) {
            $total = $p['price'] * $qty;
            $insert = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_amount) VALUES (?, ?, ?, ?)");
            $insert->execute([$user_id, $pid, $qty, $total]);
        }
    }
    $del = $conn->prepare("DELETE FROM carts WHERE user_id=?");
    $del->execute([$user_id]);
    echo json_encode(["success" => true, "message" => "Order placed successfully!"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
