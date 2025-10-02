<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Tax
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_tax'])) {
    $name = $_POST['name']; 
    $percentage = $_POST['percentage'];
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("INSERT INTO taxes (name, percentage, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sss", $name, $percentage, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: tax.php");
    exit;
}

// Handle Toggle Enable/Disable
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_tax_id'])) {
    $id = $_POST['toggle_tax_id'];

    $stmt = $mysqli->prepare("SELECT status FROM taxes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $mysqli->prepare("UPDATE taxes SET status=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: tax.php");
    exit;
}

// Fetch taxes
$taxes = $mysqli->query("SELECT * FROM taxes ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tax Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
/* --- THEME COLORS --- */
:root {
    --color-dark-nav: #131a21; /* Very dark navbar */
    --color-sidebar: #006a4e;  /* Deep Emerald Green */
    --color-text-dark: #333333;
    --color-light-bg: #ffffff;
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
    width: 220px; height: 100vh;
    position: fixed; top: 60px; left: 0; /* below navbar */
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
    display: flex; align-items: center;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    margin: 6px 10px;
    border-radius: 6px;
    transition: background 0.3s;
}
.sidebar a:hover { background: rgba(0, 200, 83, 0.2); }
.sidebar a i { margin-right: 10px; }
.sidebar a.active {
    background: #008a6e;
    box-shadow: inset 3px 0 0 white;
}

/* --- CONTENT --- */
.content {
    margin-left: 240px;
    margin-top: 80px; /* space for navbar */
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
        <span class="navbar-brand">Admin Panel</span>
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
    <a href="tax.php" class="active"><i class="bi bi-percent"></i> Tax</a>
    <a href="new_products.php"><i class="bi bi-box-seam"></i> Product</a>
    <a href="product_purchase.php"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="orders.php"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
    <h2>Tax Management</h2>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-primary">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tax Management</li>
      </ol>
    </nav>

    <div class="card mt-3 table-container">
        <div class="card-header">
            <i class="bi bi-percent"></i> Taxes
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addTaxModal">Add</button>
        </div>
        <div class="card-body">
            <table id="taxTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tax Name</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th>Updated On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
<?php if ($taxes->num_rows > 0): ?>
    <?php while ($row = $taxes->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['percentage']) ?>%</td>
            <td>
                <?php if ($row['status'] === 'active') { ?>
                    <span class="badge bg-success">Active</span>
                <?php } else { ?>
                    <span class="badge bg-danger">Inactive</span>
                <?php } ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['updated_at'] ?></td>
            <td>
                <form action="tax.php" method="post" style="display:inline-block;">
                    <input type="hidden" name="toggle_tax_id" value="<?= $row['id'] ?>">
                    <?php if ($row['status'] === 'active') { ?>
                        <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-sm btn-success">Enable</button>
                    <?php } ?>
                </form>
                <button class="btn btn-sm btn-primary">Edit</button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center">No data available</td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Tax Modal -->
<div class="modal fade" id="addTaxModal" tabindex="-1" aria-labelledby="addTaxModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addTaxModalLabel">Add Tax</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tax Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Percentage</label>
            <input type="number" step="0.01" name="percentage" class="form-control" required>
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
          <button type="submit" name="add_tax" class="btn btn-primary">Save</button>
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
    $('#taxTable').DataTable({ "pageLength": 5 });
});
</script>
</body>
</html>
