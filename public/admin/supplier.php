<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $name = $_POST['name']; 
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("INSERT INTO suppliers (name, address, contact_no, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssss", $name, $address, $contact, $email, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: supplier.php");
    exit;
}

// Handle Toggle Enable/Disable
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_supplier_id'])) {
    $id = $_POST['toggle_supplier_id'];

    $stmt = $mysqli->prepare("SELECT status FROM suppliers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $mysqli->prepare("UPDATE suppliers SET status=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: supplier.php");
    exit;
}

// Fetch suppliers
$suppliers = $mysqli->query("SELECT * FROM suppliers ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Supplier Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    body { background-color: #f8f9fa; }
    /* Sidebar */
    .sidebar { 
        width: 220px; 
        height: 100vh; 
        position: fixed; 
        top: 60px; /* below navbar */
        left: 0; 
        background: #006a4e; 
        color: white; 
        padding-top: 20px; 
    }
    .sidebar a { 
        display: block; 
        color: white; 
        padding: 10px 15px; 
        text-decoration: none; 
    }
    .sidebar a:hover, .sidebar a.active { 
        background: #006a4e; 
        color: #fff; 
    }
    /* Navbar */
    .navbar-custom {
        background-color: #131a21; 
        height: 60px;
    }
    .admin-icon {
        font-size: 1.6rem;
        color: #fff;
        cursor: pointer;
    }
    /* Content */
    .content { 
        margin-left: 240px; 
        margin-top: 60px; 
        padding: 20px; 
    }
    /* Table */
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
    .table-container { 
        max-width: 95%; 
        margin: auto; 
    }
</style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-custom fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h5 text-white">Admin Panel</span>
        <i class="bi bi-person-circle admin-icon ms-auto" title="Admin"></i>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
    <a href="#"><i class="bi bi-tags-fill"></i> Category</a>
    <a href="#"><i class="bi bi-archive"></i> Location Rack</a>
    <a href="supplier.php" class="active"><i class="bi bi-truck"></i> Supplier</a>
    <a href="#"><i class="bi bi-building"></i> Company</a>
    <a href="#"><i class="bi bi-percent"></i> Tax</a>
    <a href="#"><i class="bi bi-box-seam"></i> Product</a>
    <a href="#"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="#"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h2>Supplier Management</h2>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-primary">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Supplier Management</li>
      </ol>
    </nav>

    <div class="card mt-3 table-container">
        <div class="card-header">
            <i class="bi bi-truck"></i> Suppliers
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addSupplierModal">Add</button>
        </div>
        <div class="card-body">
            <table id="supplierTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Supplier Name</th>
                        <th>Address</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Time and Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
<?php if ($suppliers->num_rows > 0): ?>
    <?php while ($row = $suppliers->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['contact_no']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <?php if ($row['status'] === 'active') { ?>
                    <span class="badge bg-success">Active</span>
                <?php } else { ?>
                    <span class="badge bg-danger">Inactive</span>
                <?php } ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <form action="supplier.php" method="post" style="display:inline-block;">
                    <input type="hidden" name="toggle_supplier_id" value="<?= $row['id'] ?>">
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
    <tr><td colspan="7" class="text-center">No data available</td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addSupplierModalLabel">Add Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Supplier Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact No</label>
            <input type="text" name="contact" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
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
          <button type="submit" name="add_supplier" class="btn btn-primary">Save</button>
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
    $('#supplierTable').DataTable({ "pageLength": 5 });
});
</script>
</body>
</html>
