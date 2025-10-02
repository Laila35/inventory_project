<?php
require_once __DIR__ . '/../../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

$id = intval($_GET['id']);
$res = $mysqli->query("SELECT * FROM products WHERE id=$id");
$product = $res->fetch_assoc();

$err = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $mysqli->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $update = $mysqli->query("UPDATE products SET name='$name', price='$price', quantity='$quantity' WHERE id=$id");
    if($update){
        header("Location: list.php");
        exit;
    } else {
        $err = "Error: ".$mysqli->error;
    }
}
?>

<h2>Edit Product</h2>
<?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
<form method="post">
    <input name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Product Name" required><br><br>
    <input name="price" type="number" step="0.01" value="<?php echo $product['price']; ?>" placeholder="Price" required><br><br>
    <input name="quantity" type="number" value="<?php echo $product['quantity']; ?>" placeholder="Quantity" required><br><br>
    <button>Update Product</button>
</form>
<a href="list.php">Back to Products</a>
