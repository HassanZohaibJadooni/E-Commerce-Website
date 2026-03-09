<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "unathorize";
    exit();
}

$action = $_POST['action'] ?? '';

// agr action fetch hay
if ($action == 'fetch') {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        foreach ($rows as $row) {
            echo "<tr class='text-center align-middle'>
                <td>{$row['id']}</td>
                <td><img src='uploads/{$row['image']}' width='60' height='50'></td>
                <td>{$row['name']}</td>
                <td>Rs {$row['price']}</td>
                <td>{$row['description']}</td>
                <td>
                    <button class='btn btn-m btn-warning editBtn' data-id='{$row['id']}'>Edit</button>
                    <button class='btn btn-m btn-danger deleteBtn' data-id='{$row['id']}'>Delete</button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>No products found</td></tr>";
    }
} elseif ($action == 'add') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $image, $desc]);
    echo "Product added successfully!";
} elseif ($action == 'post') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
} elseif ($action == 'update') {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $oldImg = $stmt->fetchColumn();

    if (!empty($_FILES['image']['name'])) {
        if (file_exists("uploads/" . $oldImg)) unlink("uploads/" . $oldImg);
        $newImg = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $newImg);
    } else {
        $newImg = $oldImg; // wrna old img wo new img ho jaegah
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image=?, description=? WHERE id=?");
    $stmt->execute([$name, $price, $newImg, $desc, $id]);
    echo "Product updated successfully!";
} elseif ($action == 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);

    $img = $stmt->fetchColumn();
    if ($img && file_exists("uploads/" . $img)) unlink("uploads/" . $img);

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);
    echo "Product deleted successfully!";
}
