<?php
require_once __DIR__ . '/../../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

// Delete product
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM products WHERE id=$id");
    header("Location: list.php");
    exit;
}

// Fetch products
$res = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
?>

<h2>Products</h2>
<a href="new.php">Add Product</a>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Actions</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['price']; ?></td>
    <td><?php echo $row['quantity']; ?></td>
    <td>
        <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> | 
        <a href="list.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
