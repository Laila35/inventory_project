<?php
require_once __DIR__ . '/../includes/db.php';

session_start();
$errLogin = '';
$errRegister = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // LOGIN
        $email = $mysqli->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        $res = $mysqli->query("SELECT id, name, password FROM users WHERE email='$email'");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                header('Location: ../dashboard.php');
                exit;
            } else {
                $errLogin = 'Invalid credentials';
            }
        } else {
            $errLogin = 'Invalid credentials';
        }
    }

    if (isset($_POST['register'])) {
        // REGISTER
        $name = $mysqli->real_escape_string($_POST['name']);
        $email = $mysqli->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $check = $mysqli->query("SELECT id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $errRegister = "Email already exists";
        } else {
            $res = $mysqli->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')");
            if ($res) {
                header('Location: auth.php'); // back to login
                exit;
            } else {
                $errRegister = "Error: " . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Auth - Inventory Management</title>
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
      background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
      z-index:-1;
    }
    .login-container { width:350px; height:450px; perspective:1000px; }
    .card {
      width:100%; height:100%;
      position:relative;
      transform-style:preserve-3d;
      transition:transform 0.8s;
    }
    .card .front, .card .back {
      width:100%; height:100%; position:absolute;
      backface-visibility:hidden;
      background:rgba(255,255,255,0.1);
      border:1px solid rgba(255,255,255,0.2);
      border-radius:15px;
      backdrop-filter:blur(10px);
      -webkit-backdrop-filter:blur(10px);
      box-shadow:0 10px 25px rgba(0,255,153,0.3);
      padding:30px;
      display:flex; flex-direction:column; justify-content:center; align-items:center;
    }
    .card .back { transform:rotateY(180deg); }
    h2 {margin-bottom:15px; color:#00ff99; text-shadow:0 0 10px #00ff99;}
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
    .switch { margin-top:10px; font-size:14px; color:#b3ffd9; }
    .switch a { color:#00ff99; text-decoration:none; font-weight:bold; text-shadow:0 0 5px #00ff99; }
  </style>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
<div id="particles-js"></div>
<div class="login-container">
  <div class="card" id="card">
    <!-- LOGIN -->
    <div class="front">
      <h2>User Login</h2>
      <?php if($errLogin): ?><div class="error"><?= $errLogin ?></div><?php endif; ?>
      <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
        <p class="switch">Don't have an account? <a href="#" id="flipToRegister">Register</a></p>
      </form>
    </div>
    <!-- REGISTER -->
    <div class="back">
      <h2>Register</h2>
      <?php if($errRegister): ?><div class="error"><?= $errRegister ?></div><?php endif; ?>
      <form method="post">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
        <p class="switch">Already have an account? <a href="#" id="flipToLogin">Login</a></p>
      </form>
    </div>
  </div>
</div>
<script>
const card = document.getElementById("card");
document.getElementById("flipToRegister").addEventListener("click", e=>{
  e.preventDefault(); card.style.transform="rotateY(180deg)";
});
document.getElementById("flipToLogin").addEventListener("click", e=>{
  e.preventDefault(); card.style.transform="rotateY(0deg)";
});
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
