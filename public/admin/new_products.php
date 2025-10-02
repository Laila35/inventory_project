<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_new_product'])) {
    $product_name = $_POST['product_name'];
    $company = $_POST['company'];
    $location_rack = $_POST['location_rack'];
    $available_quantity = $_POST['available_quantity'];
    $status = $_POST['status'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $mysqli->prepare("INSERT INTO new_products (product_name, company, location_rack, available_quantity, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $product_name, $company, $location_rack, $available_quantity, $status, $created_at);
    $stmt->execute();
    $stmt->close();

    header("Location: new_products.php");
    exit;
}

// Toggle Enable/Disable Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_new_product_id'])) {
    $id = (int)$_POST['toggle_new_product_id'];

    $stmt = $mysqli->prepare("SELECT status FROM new_products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $mysqli->prepare("UPDATE new_products SET status=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: new_products.php");
    exit;
}

// Fetch products
$products = $mysqli->query("SELECT * FROM new_products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>New Product Management</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
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
    min-height: 100vh;
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

/* --- NAVBAR --- */
.navbar-custom {
    background: var(--color-dark-nav);
    padding: 10px 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    z-index: 1000;
}
.navbar-custom .admin-icon {
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
}

/* --- CONTENT --- */
.main-content {
    margin-left: 240px;
    margin-top: 80px; /* space for navbar */
    padding: 20px;
    color: var(--color-text-dark);
}
.breadcrumb-custom {
    margin: 20px 0;
    font-size: 14px;
    color: #6c757d;
}
.breadcrumb-custom a {
    color: #0d6efd;
    text-decoration: none;
}
.breadcrumb-custom a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand text-white">Admin Panel</span>
        <i class="bi bi-person-circle admin-icon ms-auto" title="Admin"></i>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
    <a href="category.php"><i class="bi bi-tags-fill"></i> Category</a>
    <a href="location_rack.php"><i class="bi bi-archive"></i> Location Rack</a>
    <a href="company.php"><i class="bi bi-building"></i> Company</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="tax.php"><i class="bi bi-percent"></i> Tax</a>
    <a href="new_products.php" class="active"><i class="bi bi-box-seam"></i> Product</a>
    <a href="product_purchase.php"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="orders.php"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="main-content">
    <h2>Product Management</h2>

    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <a href="dashboard.php">Dashboard</a> / Product Management
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-box-seam"></i> Products
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
        </div>
        <div class="card-body">
            <table id="newProductTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Company</th>
                        <th>Location Rack</th>
                        <th>Available Quantity</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th>Updated On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['company']) ?></td>
                                <td><?= htmlspecialchars($row['location_rack']) ?></td>
                                <td><?= htmlspecialchars($row['available_quantity']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td><?= htmlspecialchars($row['updated_at'] ?? '-') ?></td>
                                <td>
                                    <form action="new_products.php" method="post" style="display:inline-block;">
                                        <input type="hidden" name="toggle_new_product_id" value="<?= (int)$row['id'] ?>">
                                        <?php if ($row['status'] === 'active') { ?>
                                            <button type="submit" class="btn btn-sm btn-success">Enabled</button>
                                        <?php } else { ?>
                                            <button type="submit" class="btn btn-sm btn-danger">Disabled</button>
                                        <?php } ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No products available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location Rack</label>
                        <input type="text" name="location_rack" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available Quantity</label>
                        <input type="number" name="available_quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_new_product" class="btn btn-primary">Save</button>
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
    $('#newProductTable').DataTable({ pageLength: 5 });
});
</script>
</body>
</html>
