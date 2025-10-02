<?php
require_once __DIR__ . '/../../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

$err = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $mysqli->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $res = $mysqli->query("INSERT INTO products (name, price, quantity) VALUES ('$name', '$price', '$quantity')");
    if($res){
        header("Location: list.php");
        exit;
    } else {
        $err = "Error: " . $mysqli->error;
    }
}
?>

<h2>Add Product</h2>
<?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
<form method="post">
    <input name="name" placeholder="Product Name" required><br><br>
    <input name="price" type="number" step="0.01" placeholder="Price" required><br><br>
    <input name="quantity" type="number" placeholder="Quantity" required><br><br>
    <button>Add Product</button>
</form>
<a href="list.php">Back to Products</a>
