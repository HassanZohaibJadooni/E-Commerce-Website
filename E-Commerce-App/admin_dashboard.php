<?php
require "config.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}
?>
<!doctype html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!-- nav bar admin dashboard -->
  <nav class="navbar position-sticky navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <h3 class="navbar-brand">Admin Dash_Board</h3>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a href="products.php" class="btn btn-dark btn-m">
              Products List
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-dark btn-m" href="logout.php">Log Out</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

    <div class="container mt-4">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card text-center bg-primary text-white shadow-lg">
            <div class="card-body">
              <h5 class="card-title">Manage Products</h5>
              <p class="card-text">Add, edit, or delete products from the store.</p>
              <a href="products.php" class="btn btn-light w-100">Go to Products</a>
            </div>
          </div>
        </div> 
        <div class="col-md-6">
          <div class="card text-center bg-primary text-white shadow-lg">
            <div class="card-body">
              <h5 class="card-title">Details Customers</h5>
              <p class="card-text">View Users And his purchase history!</p>
              <a href="customers.php" class="btn btn-light w-100">Go to own customers details</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>

