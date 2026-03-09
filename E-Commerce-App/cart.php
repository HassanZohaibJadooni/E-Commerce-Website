<?php
require "config.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM carts WHERE user_id=?");
$stmt->execute([$user_id]);
$carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartData = [];
foreach ($carts as $item) {
  $pid = $item['product_id'];
  $qty = $item['quantity'];

  $pstmt = $conn->prepare("SELECT name, image, price FROM products WHERE id=?");
  $pstmt->execute([$pid]);
  $product = $pstmt->fetch(PDO::FETCH_ASSOC);

  if ($product) {
    $cartData[] = [
      'product_id' => $pid,
      'name' => $product['name'],
      'image' => $product['image'],
      'quantity' => $qty,
      'price' => $product['price']
    ];
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="bootstrap.css">
  <script src="jquerylibrary.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container-fluid">
      <h3 class="navbar-brand">Your Cart</h3>
      <ul class="navbar-nav ms-auto">
        <li><a href="user_dashboard.php" class="btn btn-dark">Dashboard</a></li>
        <li><a href="logout.php" class="btn btn-dark">Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="container mt-3">
    <table class="table table-bordered text-center">
      <thead class="table-danger">
        <tr>
          <th>Product ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Quantity</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($cartData): foreach ($cartData as $c): ?>
            <tr data-id="<?= $c['product_id'] ?>">
              <td><?= $c['product_id'] ?></td>
              <td><img src="uploads/<?= $c['image'] ?>" width="50" height="40"></td>
              <td><?= $c['name'] ?></td>
              <td><?= $c['quantity'] ?></td>
              <td><button class="btn btn-danger removeBtn">Remove</button></td>
            </tr>
          <?php endforeach;
        else: ?>
          <tr>
            <td colspan="5">No items in cart.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if ($cartData): ?>
      <button id="checkoutBtn" class="btn btn-success">Proceed to Checkout</button>
    <?php endif; ?>
  </div>

  <script>
    $(document).ready(function() {
      $(".removeBtn").click(function() {
        let row = $(this).closest("tr");
        let pid = row.data("id");
        $.post("cart_action.php", {
          action: "remove",
          product_id: pid
        }, function(res) {
          alert(res.message);
          if (res.success) {
            location.reload();
          }
        }, "json");
      });
      $("#checkoutBtn").click(function() {
        window.location.href = "checkout.php";
      });
    });
  </script>
</body>

</html>