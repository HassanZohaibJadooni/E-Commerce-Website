<?php
require "config.php";

// Only admin can view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    header("Location: customers.php");
    exit;
}

$user_id = $_GET['user_id'];

// get user info
$ustmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$ustmt->execute([$user_id]);
$user = $ustmt->fetch(PDO::FETCH_ASSOC);

// get all orders of this user
$ostmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY date DESC");
$ostmt->execute([$user_id]);
$orders = $ostmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="bootstrap.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <h3 class="navbar-brand">Orders</h3>
            <ul class="navbar-nav ms-auto">
                <li><a href="customers.php" class="btn btn-dark">Back</a></li>
                <li><a href="logout.php" class="btn btn-dark">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-3">
        <table class="table table-bordered text-center mt-3">
            <thead class="table-danger">
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders) {
                    foreach ($orders as $o):
                        $pstmt = $conn->prepare("SELECT name, image FROM products WHERE id=?");
                        $pstmt->execute([$o['product_id']]);
                        $p = $pstmt->fetch(PDO::FETCH_ASSOC);
                ?>
                        <tr>
                            <td><?= $o['id'] ?></td>
                            <td><?= $p['name'] ?? 'Unknown' ?></td>
                            <td><img src="uploads/<?= $p['image'] ?>" width="60" height="50"></td>
                            <td><?= $o['quantity'] ?></td>
                            <td>Rs <?= $o['total_amount'] ?></td>
                            <td><?= date("d/m/Y - l - h:i A", strtotime($o['date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">No Orders Found.</td>
                    </tr>
                <?php }; ?>
            </tbody>
        </table>
    </div>
</body>

</html>