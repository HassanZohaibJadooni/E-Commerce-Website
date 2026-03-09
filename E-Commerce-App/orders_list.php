<?php
require "config.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="bootstrap.css">
</head>

<body>
  <nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container-fluid">
      <h3 class="navbar-brand">Past Orders</h3>
      <ul class="navbar-nav ms-auto">
        <li><a href="user_dashboard.php" class="btn btn-dark">Dashboard</a></li>
        <li><a href="logout.php" class="btn btn-dark">Logout</a></li>
      </ul>
    </div>
  </nav>
<p class="text-white  text-center bg-danger">Your order History</p>
  <div class="container mt-2">
    <table class="table table-bordered text-center">
      <thead class="table-danger">
        <tr>
          <th>Order ID</th>
          <th>Product ID</th>
          <th>Product Name</th>
          <th>Image</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o):
          $pstmt = $conn->prepare("SELECT image, name FROM products WHERE id=?");
          $pstmt->execute([$o['product_id']]);
          $p = $pstmt->fetch(PDO::FETCH_ASSOC);
        ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= $o['product_id'] ?></td>
            <td><?= $p['name'] ?? 'Unknown' ?></td>
            <td class="rounded-3"><?="<img src='uploads/{$p['image']}' width='60' height='50'>"?></td>
            <td><?= $o['quantity'] ?></td>
            <td>Rs <?= $o['total_amount'] ?></td>
            <td><?= date("d / m / Y - l h:i: a", strtotime($o['date'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
</body>
</html>