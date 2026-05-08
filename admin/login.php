<?php
session_start();
require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {

        $_SESSION['user_id'] = $admin['user_id'];
        $_SESSION['is_admin'] = 1;

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Invalid admin credentials!";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/login.css"> 
  <!-- ✅ SAME CSS AS USER LOGIN -->
  

  <title>Admin Login</title>
</head>

<body>

<div class="wrapper">

    <!-- LEFT PANEL -->
    <div class="left-panel">

        <div class="content">

            <img src="../assets/images/Logo-color.png" class="logo" alt="Logo">

            <div class="handshake-container">
                <img src="../assets/images/handshake.png" class="handshake" alt="Handshake">
            </div>

            <h2 class="headline">Admin Panel Access</h2>

            <p class="description">
                Secure login for system administrators.
            </p>

        </div>

    </div>

    <!-- RIGHT PANEL -->
    <div class="container">

        <div class="login-form">

            <h1>Administrator Access</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn">
                    LOGIN
                </button>

            </form>

            <p>
                User? <a href="../login.php">Login here</a>
            </p>

        </div>

    </div>

</div>

</body>
</html>