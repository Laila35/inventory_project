<?php
require_once __DIR__ . '/../../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

// Fetch sales
$res = $mysqli->query("SELECT * FROM sales ORDER BY id DESC");
?>

<h2>Invoices</h2>
<a href="new.php">Create New Invoice</a>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Date</th>
    <th>Action</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
    <td><?php echo $row['created_at']; ?></td>
    <td><a href="new.php?sale_id=<?php echo $row['id']; ?>">View</a></td>
</tr>
<?php endwhile; ?>
</table>
