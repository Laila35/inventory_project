<?php
// Database credentials
define('DB_HOST','localhost');
define('DB_USER','root');     // default XAMPP MySQL user
define('DB_PASS','');         // default is empty
define('DB_NAME','inventory_db');

// Connect to MySQL
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
