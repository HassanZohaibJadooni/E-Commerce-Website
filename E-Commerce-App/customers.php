<?php
require "config.php";

// Only admin access
if (!isset($_SESSION['user_id']) and $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// Get all users
$stmt = $conn->query("SELECT * FROM users WHERE role='user'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="bootstrap.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <h3 class="navbar-brand">All Customers</h3>
    <ul class="navbar-nav ms-auto">
      <li><a href="admin_dashboard.php" class="btn btn-dark">Dashboard</a></li>
      <li><a href="logout.php" class="btn btn-dark">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4">
  <table class="table table-bordered text-center">
    <thead class="table-dark">
      <tr>
        <th>Customer ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= $u['user_name'] ?></td>
          <td><?= $u['email'] ?></td>
          <td>
            <a href="user_orders.php?user_id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">
              View Order History
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
