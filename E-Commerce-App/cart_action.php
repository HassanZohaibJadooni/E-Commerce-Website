<?php
require "config.php";
header('Content-Type: application/json');

$response = ["success" => false, "message" => "Invalid Request"];

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  echo json_encode($response);
  exit;
}

if (!isset($_SESSION['user_id'])) {
  $response['message'] = 'Login required';
  echo json_encode($response);
  exit;
}

$user_id    = $_SESSION['user_id'];
$action     = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? null;
$quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

try {
  //  Add to Cart
  if ($action === "add" && $product_id) {
    // check existing product
    $stmt = $conn->prepare("SELECT quantity FROM carts WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch();

    if ($item) {
      $newQty = $item['quantity'] + $quantity;
      $update = $conn->prepare("UPDATE carts SET quantity=? WHERE user_id=? AND product_id=?");
      $update->execute([$newQty, $user_id, $product_id]);
      $response['message'] = "Quantity updated in cart";
    } else {
      $insert = $conn->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
      $insert->execute([$user_id, $product_id, $quantity]);
      $response['message'] = "Product added to cart";
    }
    $response['success'] = true;
  }

  //  Count total items
  if ($action === "count") {
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM carts WHERE user_id=?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn() ?? 0;
    $response = ['success' => true, 'count' => (int)$count];
  }

  //  Remove product
  if ($action === "remove" && $product_id) {
    $stmt = $conn->prepare("DELETE FROM carts WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);
    $response = ['success' => true, 'message' => 'Product deleted from cart'];
  }
} catch (PDOException $e) {
  $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>
