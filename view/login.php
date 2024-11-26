<?php
include '../db/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumi Login Page</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
        </div>
        <div class="auth-buttons">
            <a href="../view/index.php" class="btn btn-login">Home</a>
            <a href="../view/register.php" class="btn btn-register">Register</a>
        </div>
    </nav>

    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }
        ?>
        <form id="loginForm" method="POST" action="../actions/login_user.php">
            <input type="text" id="username" name="email" required placeholder="Enter your email">
            <span style="color: tomato" id="usernameError" class="error"></span>
    
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            <span style="color: tomato" id="passwordError" class="error"></span>
    
            <input type="submit" value="Login">
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Kumi - All rights reserved.</p>
    </footer>

    <style>
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</body>
</html>