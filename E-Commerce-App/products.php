<?php
require "config.php";

// agar login nahi kiya howa to redirect kar do login.php par
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.css">
    <script src="jquerylibrary.js"></script>
    <script src="boostrap.js"></script>
</head>

<body class="bg-light">

    <!-- Nav bar product list -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <h3 class="navbar-brand">Products</h3>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="btn btn-dark btn-m" href="admin_dashboard.php">Admin Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-dark btn-m" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
<br> <br>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#productModal">
            Add New Product
        </button>

        <!-- Product List -->
        <div class="table-responsive mt-1">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-danger text-center">
                    <tr>
                        <th>Product ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <tr>
                        <td colspan="6" class="text-center">No Available Data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Form model form add product and edit product  -->
                <form id="productForm" method="post" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add / Edit Product</h5>

                        <!-- model close -->
                        <button type="button" class="btn-close close_btn" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="product_id">
                        <input type="hidden" id="action" name="action" value="add">

                        <div class="mb-3">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="image">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div id="currentImage" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jquery for product list -->
    <script>
        $(document).ready(function() {

            fetchProducts();
            // Fetch all products
            function fetchProducts() {
                $.post('product_actions.php', {
                    action: 'fetch'
                }, function(data) {
                    $('#productTableBody').html(data);
                });
            }

            // Add / Update Product
            $('#productForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'product_actions.php',
                    type: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#productModal').modal('hide'); // close modal 
                        $('#productForm')[0].reset(); // reset form
                        $('#currentImage').html(''); // remove old image 
                        fetchProducts(); // reload list
                        alert(res); // show alert
                    },
                    error: function() {
                        alert('Something went wrong!');
                    }
                });
            });

            $(".close_btn").on("click", function() {
                $('#productModal').modal('hide'); // close modal 
                $('#productForm')[0].reset(); // reset form
                $('#currentImage').html(''); // remove old image 
            })
            // Edit Product
            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id');
                $.post('product_actions.php', {
                    action: 'post',
                    id: id
                }, function(res) {
                    const data = JSON.parse(res);
                    $('#product_id').val(data.id);
                    $('#name').val(data.name);
                    $('#price').val(data.price);
                    $('#description').val(data.description);
                    $('#currentImage').html(`<img src="uploads/${data.image}" width="80" class="mt-2">`);
                    $('#action').val('update');
                    $('#productModal').modal('show');
                });
            });

            // Delete Product
            $(document).on('click', '.deleteBtn', function() {
                if (!confirm('Are you sure you want to delete this product?')) return;
                const id = $(this).data('id');
                $.post('product_actions.php', {
                    action: 'delete',
                    id: id
                }, function(res) {
                    fetchProducts(); 
                    alert(res);
                });
            });
        });
    </script>

</body>

</html>