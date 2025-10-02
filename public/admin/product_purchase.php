<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Purchase
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_purchase'])) {
    $product_name = $_POST['product_name'];
    $batch_no = $_POST['batch_no'];
    $supplier = $_POST['supplier'];
    $quantity = $_POST['quantity'];
    $available_qty = $_POST['available_qty'];
    $price_per_unit = $_POST['price_per_unit'];
    $total_cost = $quantity * $price_per_unit;
    $mfr_date = $_POST['mfr_date'];
    $expiry_date = $_POST['expiry_date'];
    $sales_price = $_POST['sales_price'];
    $purchase_date = $_POST['purchase_date'];
    $status = $_POST['status'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $mysqli->prepare("INSERT INTO product_purchase 
        (product_name, batch_no, supplier, quantity, available_qty, price_per_unit, total_cost, mfr_date, expiry_date, sales_price, purchase_date, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiidsssssss", $product_name, $batch_no, $supplier, $quantity, $available_qty, $price_per_unit, $total_cost, $mfr_date, $expiry_date, $sales_price, $purchase_date, $status, $created_at);
    $stmt->execute();
    $stmt->close();

    header("Location: product_purchase.php");
    exit;
}

// Fetch purchases
$purchases = $mysqli->query("SELECT * FROM product_purchase ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Product Purchase Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
/* --- THEME COLORS --- */
:root {
    --color-dark-nav: #131a21; /* Navbar */
    --color-sidebar: #006a4e;  /* Sidebar */
    --color-sidebar-active: #008a6e;
    --color-hover: rgba(0, 200, 83, 0.2);
    --color-light-bg: #ffffff;
    --color-text-dark: #333333;
}

/* --- GENERAL --- */
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: var(--color-light-bg);
    color: var(--color-text-dark);
    margin: 0;
}

/* --- NAVBAR --- */
.navbar-custom {
    background: var(--color-dark-nav);
    padding: 10px 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    z-index: 1000;
}
.navbar-custom .navbar-brand {
    color: white;
    font-weight: 600;
}
.navbar-custom .admin-icon {
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
}

/* --- SIDEBAR --- */
.sidebar {
    width: 220px;
    height: 100vh;
    position: fixed;
    top: 60px;
    left: 0;
    background: var(--color-sidebar);
    color: white;
    padding-top: 20px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.15);
}
.sidebar h5 {
    text-align: center;
    margin-bottom: 30px;
    color: white;
}
.sidebar a {
    display: flex;
    align-items: center;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    margin: 6px 10px;
    border-radius: 6px;
    transition: background 0.3s;
}
.sidebar a:hover { background: var(--color-hover); }
.sidebar a.active {
    background: var(--color-sidebar-active);
    box-shadow: inset 3px 0 0 white;
}
.sidebar a i { margin-right: 10px; }

/* --- CONTENT --- */
.content {
    margin-left: 240px;
    margin-top: 80px; /* navbar space */
    padding: 20px;
    color: var(--color-text-dark);
}

/* --- TABLES --- */
.table thead th { 
    text-align: center; 
    vertical-align: middle; 
    padding: 10px; 
    font-size: 14px; 
}
.table tbody td { 
    text-align: center; 
    vertical-align: middle; 
    font-size: 14px; 
}
.table-container { max-width: 95%; margin: auto; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">Admin Panal</span>
        <i class="bi bi-person-circle admin-icon ms-auto" title="Admin"></i>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
    <a href="category.php"><i class="bi bi-tags-fill"></i> Category</a>
    <a href="location_rack.php"><i class="bi bi-archive"></i> Location Rack</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="tax.php"><i class="bi bi-percent"></i> Tax</a>
    <a href="new_products.php"><i class="bi bi-box-seam"></i> Product</a>
    <a href="product_purchase.php" class="active"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="orders.php"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
    <h2>Product Purchase</h2>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-primary">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Product Purchase</li>
      </ol>
    </nav>

    <div class="card mt-3 table-container">
        <div class="card-header">
            <i class="bi bi-bag-plus-fill"></i> Purchases
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">Add Purchase</button>
        </div>
        <div class="card-body">
            <table id="purchaseTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Batch No</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Available Qty</th>
                        <th>Price per Unit</th>
                        <th>Total Cost</th>
                        <th>Mfr. Date</th>
                        <th>Expiry Date</th>
                        <th>Sales Price</th>
                        <th>Purchase Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
<?php if ($purchases && $purchases->num_rows > 0): ?>
    <?php while ($row = $purchases->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['batch_no']) ?></td>
            <td><?= htmlspecialchars($row['supplier']) ?></td>
            <td><?= htmlspecialchars($row['quantity']) ?></td>
            <td><?= htmlspecialchars($row['available_qty']) ?></td>
            <td><?= htmlspecialchars($row['price_per_unit']) ?></td>
            <td><?= htmlspecialchars($row['total_cost']) ?></td>
            <td><?= htmlspecialchars($row['mfr_date']) ?></td>
            <td><?= htmlspecialchars($row['expiry_date']) ?></td>
            <td><?= htmlspecialchars($row['sales_price']) ?></td>
            <td><?= htmlspecialchars($row['purchase_date']) ?></td>
            <td>
                <span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'danger' ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="12" class="text-center">No purchases available</td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Purchase Modal -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Batch No</label>
                        <input type="text" name="batch_no" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Supplier</label>
                        <input type="text" name="supplier" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Available Qty</label>
                        <input type="number" name="available_qty" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price per Unit</label>
                        <input type="number" step="0.01" name="price_per_unit" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mfr. Date</label>
                        <input type="date" name="mfr_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sales Price</label>
                        <input type="number" step="0.01" name="sales_price" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_purchase" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#purchaseTable').DataTable({ "pageLength": 5 });
});
</script>
</body>
</html>
