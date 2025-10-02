<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name']; 
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // convert user_type to is_admin (1 = Admin, 0 = Staff)
    $is_admin = ($_POST['user_type'] === 'admin') ? 1 : 0;
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, is_admin, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $name, $email, $password, $is_admin, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: users.php");
    exit;
}

// Handle Toggle Enable/Disable User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_user_id'])) {
    $id = $_POST['toggle_user_id'];

    $stmt = $mysqli->prepare("SELECT status FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $mysqli->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: users.php");
    exit;
}

// Handle Edit User (placeholder)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user_id'])) {
    $id = $_POST['edit_user_id'];
    // later logic here
}

// Fetch users
$users = $mysqli->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
/* --- THEME COLORS --- */
:root {
    --color-dark-nav: #131a21; /* Very dark navbar */
    --color-sidebar: #006a4e; /* Deep Emerald Green */
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
    height: 60px;
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
    position: fixed; top: 60px; left: 0; /* now below navbar */
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
.table-container {
    max-width: 95%;
    margin: auto;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">User Management</span>
        <i class="bi bi-person-circle admin-icon ms-auto" title="Admin"></i>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <h5>Admin Login</h5>
    <a href="dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
    <a href="users.php" class="active"><i class="bi bi-people-fill"></i> Users</a>
    <a href="category.php"><i class="bi bi-tags-fill"></i> Category</a>
    <a href="location_rack.php"><i class="bi bi-archive"></i> Location Rack</a>
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
    <h2>User Management</h2>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="dashboard.php" class="text-primary">Dashboard</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          User Management
        </li>
      </ol>
    </nav>

    <div class="card mt-3 table-container">
        <div class="card-header">
            <i class="bi bi-people-fill"></i> User Management
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">Add</button>
        </div>
        <div class="card-body">
            <table id="userTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width:20%;">User Name</th>
                        <th style="width:25%;">User Email Address</th>
                        <th style="width:15%;">Password</th>
                        <th style="width:15%;">User Type</th>
                        <th style="width:10%;">Status</th>
                        <th style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>********</td>
                            <td><?= $row['is_admin'] ? 'Admin' : 'Staff' ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <!-- Edit Button -->
                                <form action="users.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="edit_user_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Edit</button>
                                </form>
                                <!-- Enable/Disable Button -->
                                <form action="users.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="toggle_user_id" value="<?= $row['id']; ?>">
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
                    <tr><td colspan="6" class="text-center">No data available in table</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">User Type</label>
            <select name="user_type" class="form-select">
              <option value="admin">Admin</option>
              <option value="staff">Staff</option>
            </select>
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
          <button type="submit" name="add_user" class="btn btn-primary">Save</button>
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
    $('#userTable').DataTable({
        "pageLength": 5
    });
});
</script>
</body>
</html>
