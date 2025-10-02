<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: auth.php');
    exit;
}

$res = $mysqli->query("SELECT name FROM users WHERE id=".intval($_SESSION['user_id']));
$user = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        background: #f9fafb;
    }

    /* Navbar */
    .navbar {
        height: 60px;
        /* Updated: Darker background to match the login theme */
        background: #131a21; 
        /* Updated: White text for visibility on dark background */
        color: white; 
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3); /* Slightly stronger shadow for depth */
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    .navbar .title {
        font-size: 20px;
    }
    .navbar .user-menu {
        position: relative;
    }
    .navbar .user-icon {
        cursor: pointer;
        font-size: 22px;
        /* Updated: White icon for contrast */
        color: white; 
        transition: opacity 0.3s;
    }
    .navbar .user-icon:hover {
        opacity: 0.8;
    }
    .navbar .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 50px;
        background: white;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .navbar .dropdown a {
        display: block;
        padding: 10px 15px;
        text-decoration: none;
        color: #333;
        transition: background 0.3s;
    }
    .navbar .dropdown a:hover {
        /* Adjusted hover color to a light green for theme consistency */
        background: #e6ffe6; 
        color: #006a4e;
    }

    /* Sidebar */
    .sidebar {
        width: 220px;
        height: 100vh;
        /* Updated: Solid Deep Emerald Green background */
        background: #006a4e; 
        color: white;
        position: fixed;
        top: 60px;
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
        /* Adjusted hover to a semi-transparent brighter green */
        background: rgba(0, 200, 83, 0.2); 
    }
    .sidebar i {
        margin-right: 10px;
    }
    /* Active Link Style - a recommended addition */
    .sidebar a.active {
        background: #008a6e; /* A slightly brighter shade for the current page */
        box-shadow: inset 3px 0 0 white; /* A subtle white bar for emphasis */
    }

    /* Content */
    .content {
        margin-left: 220px;
        margin-top: 60px;
        padding: 20px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <div class="title">User Dashboard</div>
        <div class="user-menu">
            <i class="fas fa-user user-icon" onclick="toggleDropdown()"></i> 
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="sidebar">
        <a href="products/list.php" class="active"><i class="fas fa-box"></i> Products</a>
        <a href="sales/new.php"><i class="fas fa-shopping-cart"></i> Sales</a>
    </div>

    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p>Choose an option from the sidebar.</p>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            // Toggle visibility using block/none
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
        window.onclick = function(e) {
            // Close dropdown if click is outside the user icon
            if (!e.target.matches('.user-icon')) {
                const dropdown = document.getElementById('dropdownMenu');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>