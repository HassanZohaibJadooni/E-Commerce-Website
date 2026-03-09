<?php
require "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all products
$stmt = $conn->query("SELECT * FROM products ORDER BY id ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap.css" rel="stylesheet">
    <script src="boostrap.js"></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container-fluid">
            <h3 class="navbar-brand">User Dash Board</h3>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-dark btn-m" href="orders_list.php">Past Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-m btn-dark" href="cart.php">
                            Carts <span id="cartCount" class="badge rounded-pill bg-success cart-badge">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-dark btn-m" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- products available -->
    <div class="container mt-4">
        <div class="row" id="productList">
            <?php if ($products): foreach ($products as $p): ?>
                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm">
                            <img src="uploads/<?= $p['image'] ?>" class="card-img-top" height="100" style="object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= $p['name'] ?></h5>
                                <p class="text-success fw-bold">Rs <?= $p['price'] ?></p>

                                <!--  Quantity Input -->
                                <input type="number" min="1" value="1" class="form-control mb-2 qtyInput" style="width:90px;display:inline-block;">
                                <!-- Add to cart button -->
                                <button class="btn btn-primary w-30 addToCartBtn" data-id="<?= $p['id'] ?>">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <p class="text-center text-danger">No products available</p>
            <?php endif; ?>
        </div>
</body>
<script src="jquerylibrary.js"></script>

<!-- jquery code -->
<script>
    $(document).ready(function() {
        $(".addToCartBtn").click(function() {
            let pid = $(this).data("id");
            let qty = $(this).closest(".card-body").find(".qtyInput").val(); // fixed line

            $.ajax({
                url: "cart_action.php",
                type: "POST",
                data: {
                    action: "add",
                    product_id: pid,
                    quantity: qty
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        updateCartCount();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Ajax Error: " + error);
                }
            });
        });

        function updateCartCount() {
            $.ajax({
                url: "cart_action.php",
                type: "POST",
                data: {
                    action: "count"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $("#cartCount").text(response.count);
                    }
                }
            });
        }
    });
</script>


</html>