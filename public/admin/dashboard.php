<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch dashboard stats
$inStock = $mysqli->query("SELECT COUNT(*) as c FROM products WHERE quantity > 0")->fetch_assoc()['c'];
$outStock = $mysqli->query("SELECT COUNT(*) as c FROM products WHERE quantity <= 0")->fetch_assoc()['c'];
$totalPurchase = $mysqli->query("SELECT SUM(price * quantity) as total FROM products")->fetch_assoc()['total'] ?? 0;
$totalSales = $mysqli->query("SELECT SUM(si.quantity * p.price) as total 
    FROM sale_items si 
    JOIN products p ON si.product_id=p.id")->fetch_assoc()['total'] ?? 0;

// Example sales data for last 6 days
$salesLabels = [];
$salesValues = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date("d-m-Y", strtotime("-$i days")); // only day-month-year
    $salesLabels[] = $date;
    $salesValues[] = rand(1000, 5000); // dummy values
}

// Fetch out of stock products
$outProducts = $mysqli->query("SELECT p.*, c.name as company, r.name as rack 
    FROM products p 
    LEFT JOIN company c ON p.company_id=c.id 
    LEFT JOIN location_rack r ON p.rack_id=r.id 
    WHERE p.quantity <= 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

/* --- CARDS --- */
.card-box {
    padding: 20px; border-radius: 10px; color: white;
    margin-bottom: 20px; text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.card-blue { background: #007bff; }
.card-orange { background: #fd7e14; }
.card-red { background: #dc3545; }
.card-green { background: #28a745; }

/* --- SECTIONS --- */
.chart-header, .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    color: var(--color-text-dark);
    padding: 10px 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px 5px 0 0;
}
.chart-header h5, .section-header h5 {
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
}
.chart-header h5 i, .section-header h5 i {
    margin-right: 8px;
    color: #28a745; 
}

/* --- TABLES --- */
.table-responsive {
    border: 1px solid #dee2e6 !important;
}
.table-responsive .table {
    --bs-table-color: var(--color-text-dark); 
    --bs-table-bg: var(--color-light-bg); 
}
.table-responsive .table-striped > tbody > tr:nth-of-type(odd) > * {
    background-color: #f8f9fa; 
}
.table-responsive .table-dark {
    --bs-table-bg: var(--color-dark-nav); 
    --bs-table-color: white; 
    border-color: #333d47;
}

/* DataTable Overrides */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input,
.form-select {
    background-color: white !important;
    color: var(--color-text-dark) !important;
    border-color: #ced4da !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    background-color: white !important;
    color: var(--color-text-dark) !important;
    border-color: #dee2e6 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: var(--color-sidebar) !important;
    color: white !important;
    border-color: var(--color-sidebar) !important;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">User Dashboard</span>
        <i class="bi bi-person-circle admin-icon ms-auto" title="Admin"></i>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <h5>Admin Login</h5>
    <a href="dashboard.php" class="active"><i class="bi bi-grid-fill"></i> Dashboard</a>
    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
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
    <h2 class="mb-4">Dashboard</h2>
    <div class="row">
        <div class="col-md-3"><div class="card-box card-blue"><h3><?= $inStock ?></h3><p>In Stock Product</p></div></div>
        <div class="col-md-3"><div class="card-box card-orange"><h3><?= $outStock ?></h3><p>Out of Stock</p></div></div>
        <div class="col-md-3"><div class="card-box card-red"><h3>₹ <?= number_format($totalPurchase,2) ?></h3><p>Total Purchase</p></div></div>
        <div class="col-md-3"><div class="card-box card-green"><h3>₹ <?= number_format($totalSales,2) ?></h3><p>Total Sales</p></div></div>
    </div>

    <div class="mt-5">
        <div class="chart-header">
            <h5><i class="bi bi-graph-up"></i> Sale Status</h5>
            <form method="get">
                <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="today">Today</option>
                    <option value="7days">Last 7 Days</option>
                    <option value="lastmonth">Last Month</option>
                    <option value="thisyear">This Year</option>
                    <option value="lastyear">Last Year</option>
                </select>
            </form>
        </div>
        <div style="background-color: white; padding: 15px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 5px 5px;"> 
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <div class="mt-5">
        <div class="section-header">
            <h5><i class="bi bi-box-seam"></i> List of Out of Products</h5>
        </div>
        <div class="table-responsive border border-top-0 p-3">
            <table id="outProductsTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Company</th>
                        <th>Available Quantity</th>
                        <th>Location Rack</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th>Updated On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($outProducts->num_rows > 0): ?>
                    <?php while ($row = $outProducts->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['company'] ?? '-' ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['rack'] ?? '-' ?></td>
                        <td><span class="badge bg-danger">Out of Stock</span></td>
                        <td><?= $row['created_at'] ?? '-' ?></td>
                        <td><?= $row['updated_at'] ?? '-' ?></td>
                        <td><a href="#" class="btn btn-sm btn-primary">Purchase</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">No out of stock products found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Chart.js
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($salesLabels) ?>,
        datasets: [{
            label: 'Sales',
            data: <?= json_encode($salesValues) ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40,167,69,0.3)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#28a745',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { 
                ticks: { autoSkip: true, maxRotation: 45, color: 'var(--color-text-dark)' },
                grid: { color: '#e9ecef' } 
            },
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1000, color: 'var(--color-text-dark)' },
                grid: { color: '#e9ecef' } 
            }
        }
    }
});

// DataTables
$(document).ready(function () {
    $('#outProductsTable').DataTable();
});
</script>
</body>
</html>
