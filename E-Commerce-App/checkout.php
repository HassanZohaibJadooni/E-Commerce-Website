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
$finalTotal = 0;
foreach ($carts as $c) {
    $pid = $c['product_id'];
    $qty = $c['quantity'];
    $pstmt = $conn->prepare("SELECT name, price, image FROM products WHERE id=?");
    $pstmt->execute([$pid]);
    $p = $pstmt->fetch(PDO::FETCH_ASSOC);
    if ($p) {
        $total = $p['price'] * $qty;
        $finalTotal += $total;
        $cartData[] = [
            'id' => $pid,
            'name' => $p['name'],
            'image' => $p['image'],
            'price' => $p['price'],
            'qty' => $qty,
            'total' => $total
        ];
    }
}
?>

<!-- Html -->
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="bootstrap.css">
    <script src="jquerylibrary.js"></script>
</head>

<body>

    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container-fluid">
            <h3 class="navbar-brand">Check Out</h3>
            <ul class="navbar-nav ms-auto">
                <li><a href="user_dashboard.php" class="btn btn-dark">Dashboard</a></li>
                <li><a href="logout.php" class="btn btn-dark">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <table class="table table-bordered text-center mt-2">
            <thead class="table-danger">
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cartData): ?>
                    <?php foreach ($cartData as $i): ?>
                        <tr>
                            <td><img src="uploads/<?= $i['image'] ?>" width="50" height="40"></td>
                            <td><?= $i['name'] ?></td>
                            <td>Rs <?= $i['price'] ?></td>
                            <td><?= $i['qty'] ?></td>
                            <td>Rs <?= $i['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-danger">
                        <td colspan="4" class="text-end fw-bold">Final Total</td>
                        <td><b>Rs <?= $finalTotal ?></b></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
        <div class="text-end">
            <button id="confirmBtn" class="btn btn-success">Confirm Order</button>
            <a href="cart.php" class="btn btn-primary">Back to Cart</a>
        </div>
    </div>
    <script>
        $(function() {
            $("#confirmBtn").click(function() {
                $.ajax({
                    url: "checkout_action.php",
                    type: "POST",
                    dataType: "json",
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            window.location.href = "user_dashboard.php";
                            alert("Thank for confirm your order")
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>