<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Location Rack
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_location'])) {
    $name = $_POST['name']; 
    $status = $_POST['status'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $mysqli->prepare("INSERT INTO location_rack (name, created_at, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $created_at, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: location_rack.php");
    exit;
}

// Handle Toggle Enable/Disable Location Rack
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_location_id'])) {
    $id = $_POST['toggle_location_id'];

    $stmt = $mysqli->prepare("SELECT status FROM location_rack WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $mysqli->prepare("UPDATE location_rack SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: location_rack.php");
    exit;
}

// Fetch Location Racks
$locations = $mysqli->query("SELECT * FROM location_rack");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Location Rack Management</title>
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

/* GENERAL */
body {
    background: var(--color-light-bg);
    font-family: 'Segoe UI', Tahoma, sans-serif;
    color: var(--color-text-dark);
    margin: 0;
}

/* NAVBAR */
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

/* SIDEBAR */
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

/* CONTENT */
.content {
    margin-left: 240px;
    margin-top: 80px;
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
    <a href="location_rack.php" class="active"><i class="bi bi-archive"></i> Location Rack</a>
    <a href="company.php"><i class="bi bi-building"></i> Company</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="tax.php"><i class="bi bi-percent"></i> Tax</a>
    <a href="new_products.php"><i class="bi bi-box-seam"></i> Product</a>
    <a href="product_purchase.php"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="orders.php"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
    <h2>Location Rack Management</h2>

    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <a href="dashboard.php">Dashboard</a> / Location Rack Management
    </div>

    <div class="card mt-3 table-container">
        <div class="card-header">
            <i class="bi bi-archive"></i> Location Rack Management
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addLocationModal">Add</button>
        </div>
        <div class="card-body">
            <table id="locationTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width:40%;">Rack Name</th>
                        <th style="width:20%;">Date & Time</th>
                        <th style="width:20%;">Status</th>
                        <th style="width:20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
<?php if ($locations->num_rows > 0): ?>
    <?php while ($row = $locations->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'danger' ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>
            <td>
                <!-- Enable/Disable Button -->
                <form action="location_rack.php" method="post" style="display:inline-block;">
                    <input type="hidden" name="toggle_location_id" value="<?= $row['id']; ?>">
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
    <tr><td colspan="4" class="text-center">No location racks found</td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Location Rack Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Add Location Rack</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Rack Name</label>
            <input type="text" name="name" class="form-control" required>
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
          <button type="submit" name="add_location" class="btn btn-primary">Save</button>
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
    $('#locationTable').DataTable({ pageLength: 5 });
});
</script>
</body>
</html>

