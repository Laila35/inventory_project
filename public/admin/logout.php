<?php
session_start();
session_destroy();
header('Location: login.php'); // ✅ redirect back to admin login
exit;
