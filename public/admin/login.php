<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);

    $res = $mysqli->query("SELECT id, password, name FROM users WHERE email='$email' AND is_admin=1");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // your DB stores plain text for admin
        if ($row['password'] === $password) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $err = 'Invalid admin credentials';
        }
    } else {
        $err = 'Invalid admin credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    * {margin:0; padding:0; box-sizing:border-box;}
    body,html {
      height:100%;
      font-family:'Segoe UI',sans-serif;
      display:flex; justify-content:center; align-items:center;
      overflow:hidden;
    }
    #particles-js {
      position:absolute; width:100%; height:100%;
      background:linear-gradient(135deg,#141E30,#243B55);
      z-index:-1;
    }
    .login-container { width:350px; padding:30px; }
    .card {
      width:100%; padding:30px;
      background:rgba(255,255,255,0.1);
      border:1px solid rgba(255,255,255,0.2);
      border-radius:15px;
      backdrop-filter:blur(10px);
      -webkit-backdrop-filter:blur(10px);
      box-shadow:0 10px 25px rgba(0,255,153,0.3);
      display:flex; flex-direction:column;
      justify-content:center; align-items:center;
    }
    h2 {margin-bottom:20px; color:#00ff99; text-shadow:0 0 10px #00ff99;}
    .error { color:#ff8080; font-size:14px; margin-bottom:10px; text-align:center; }
    input {
      width:100%; padding:12px; margin:8px 0;
      border:1px solid rgba(0,255,153,0.6);
      border-radius:8px;
      background:rgba(255,255,255,0.15);
      color:#fff;
      outline:none;
    }
    input::placeholder { color:#b3ffd9; }
    button {
      width:100%; padding:12px; margin-top:15px;
      border:none; border-radius:8px;
      background:linear-gradient(135deg,#00ff99,#00cc66);
      color:white; font-size:16px; cursor:pointer;
      transition:0.3s;
      text-shadow:0 0 5px #00ff99,0 0 10px #00ff99;
      box-shadow:0 0 15px #00ff99;
    }
    button:hover {
      background:linear-gradient(135deg,#00e68a,#00994d);
      box-shadow:0 0 25px #00ff99,0 0 50px #00ff99;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
<div id="particles-js"></div>
<div class="login-container">
  <div class="card">
    <h2>Admin Login</h2>
    <?php if($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
    <form method="post">
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>
<script>
particlesJS("particles-js",{
  "particles":{"number":{"value":100},"size":{"value":4},"color":{"value":"#00ff99"},
  "opacity":{"value":0.8,"anim":{"enable":true,"speed":1,"opacity_min":0.3}},
  "move":{"speed":2,"out_mode":"out"},
  "line_linked":{"enable":true,"distance":150,"color":"#00ff99","opacity":0.5,"width":1}},
  "interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"grab"}},
  "modes":{"grab":{"distance":200,"line_linked":{"opacity":0.8}}}}
});
</script>
</body>
</html>
