<?php
require_once __DIR__ . '/../../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

// Fetch products
$products = $mysqli->query("SELECT * FROM products ORDER BY name");

$err = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $customer = $mysqli->real_escape_string($_POST['customer']);
    $items = $_POST['items'] ?? [];

    if(!$customer || empty($items)){
        $err = "Please enter customer name and select at least one product";
    } else {
        $mysqli->query("INSERT INTO sales (customer_name) VALUES ('$customer')");
        $sale_id = $mysqli->insert_id;

        foreach($items as $product_id => $qty){
            $product_id = intval($product_id);
            $qty = intval($qty);
            if($qty>0){
                $mysqli->query("INSERT INTO sale_items (sale_id,product_id,quantity) VALUES ($sale_id,$product_id,$qty)");
            }
        }
        header("Location: list.php");
        exit;
    }
}
?>

<h2>Create Invoice</h2>
<?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
<form method="post">
    <input name="customer" placeholder="Customer Name" required><br><br>
    <h4>Products</h4>
    <?php while($p = $products->fetch_assoc()): ?>
        <label><?php echo htmlspecialchars($p['name']); ?> (Price: <?php echo $p['price']; ?>)</label>
        <input type="number" name="items[<?php echo $p['id']; ?>]" placeholder="Quantity" min="0" value="0"><br>
    <?php endwhile; ?>
    <br>
    <button>Create Invoice</button>
</form>
<a href="list.php">Back to Invoices</a>
