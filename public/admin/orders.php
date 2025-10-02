<?php 
session_start();
require_once __DIR__ . '/../../includes/db.php'; // ✅ Correct DB include

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Order form submission
if (isset($_POST['add_order'])) {
    $order_name = $mysqli->real_escape_string($_POST['order_name']);
    $customer_name = $mysqli->real_escape_string($_POST['customer_name']);
    $total_amount = $mysqli->real_escape_string($_POST['total_amount']);
    $status = $mysqli->real_escape_string($_POST['status']);
    $created_by = $_SESSION['admin_id'];

    $sql = "INSERT INTO orders (order_name, customer_name, total_amount, status, created_by, created_at, updated_at) 
            VALUES ('$order_name', '$customer_name', '$total_amount', '$status', '$created_by', NOW(), NOW())";

    if ($mysqli->query($sql)) {
        header("Location: orders.php?success=1");
        exit;
    } else {
        echo "Error: " . $mysqli->error;
    }
}

// Fetch orders
$orders = $mysqli->query("SELECT o.*, u.name as created_by_name 
                          FROM orders o 
                          LEFT JOIN users u ON o.created_by = u.id 
                          ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f9fafb;
        }

        /* Navbar */
        .navbar-custom {
            height: 60px;
            background: #131a21; /* ✅ Dark navbar */
            color: white;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .admin-icon {
            font-size: 1.7rem;
            color: white;
            cursor: pointer;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #006a4e; /* ✅ Emerald green sidebar */
            color: white;
            position: fixed;
            top: 60px; /* below navbar */
            left: 0;
            padding-top: 20px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            margin: 6px 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: rgba(0, 200, 83, 0.2);
        }
        .sidebar a.active {
            background: #008a6e;
            box-shadow: inset 3px 0 0 white;
        }
        .sidebar i {
            margin-right: 10px;
        }

        /* Content */
        .main-content {
            margin-left: 220px;
            margin-top: 60px;
            padding: 20px;
        }

        /* Breadcrumb */
        .breadcrumb-custom {
            margin-bottom: 20px;
        }
        .breadcrumb-custom a {
            text-decoration: none;
            color: #006a4e;
            font-weight: 500;
        }

        /* Table */
        table thead th {
            background-color: #131a21 !important; /* match navbar */
            color: white !important;
            text-align: center;
        }
        table tbody td {
            text-align: center;
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
<!-- Sidebar -->
<div class="sidebar">
    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
    <a href="category.php"><i class="bi bi-tags-fill"></i> Category</a>
    <a href="location_rack.php"><i class="bi bi-archive"></i> Location Rack</a>
    <a href="company.php"><i class="bi bi-building"></i> Company</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="tax.php"><i class="bi bi-percent"></i> Tax</a>
    <a href="new_products.php"><i class="bi bi-box-seam"></i> Product</a>
    <a href="product_purchase.php"><i class="bi bi-bag-plus-fill"></i> Product Purchase</a>
    <a href="orders.php" class="active"><i class="bi bi-cart-check-fill"></i> Orders</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 style="color: #131a21;">Order Management</h2>

    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <a href="dashboard.php">Dashboard</a> / Order Management
    </div>

    <!-- Orders Card -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-cart-check-fill"></i> Orders
            <button class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addOrderModal">Add Order</button>
        </div>
        <div class="card-body">
            <table id="ordersTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order Name</th>
                        <th>Customer Name</th>
                        <th>Order Amount</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th>Updated On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_name']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['total_amount']) ?></td>
                            <td><?= htmlspecialchars($row['created_by_name'] ?? 'Admin') ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td><?= htmlspecialchars($row['updated_at']) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Edit</button>
                                <button class="btn btn-sm toggle-status <?= strtolower($row['status']) == 'enabled' ? 'btn-success' : 'btn-danger' ?>">
                                    <?= strtolower($row['status']) == 'enabled' ? 'Enabled' : 'Disabled' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Order Name</label>
                <input type="text" name="order_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Order Amount</label>
                <input type="number" name="total_amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Enabled">Enabled</option>
                    <option value="Disabled">Disabled</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_order" class="btn btn-success">Save Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    $('#ordersTable').DataTable();

    // Toggle Enabled/Disabled button
    $(document).on('click', '.toggle-status', function () {
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success').addClass('btn-danger').text('Disabled');
        } else {
            $(this).removeClass('btn-danger').addClass('btn-success').text('Enabled');
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
