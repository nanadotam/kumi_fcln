<?php
include '../db/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumi Login Page</title>
    <link rel="stylesheet" href="../assests/css/styles.css">
    <link rel="stylesheet" href="../assests/css/login.css">
</head>
<body>
    <header>
        <nav class="navbar">
          <div class="logo">
            <img src="../assests/images/KUMI_logo.svg" alt="Kumi Logo">
          </div>
          </ul>
          <div class="auth-buttons">
            <a href="../view/index.php" class="btn btn-login">Home</a>
            <a href="../view/register.php" class="btn btn-register">Register</a>
          </div>
        </nav>
      </header>
    <div class = "login-container">
        <h2>Login</h2>
        <form id="loginForm" method="POST" action="../actions/login_user.php">
            <input type="text" id="username" name="email" required placeholder="Enter your email">
            <span style="color: tomato" id="usernameError" class="error"></span>
    
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            <span style="color: tomato" id="passwordError" class="error"></span>
    
            <input type="submit" value="Login">
        </form>
    </div>
    <script>
        // Removed unnecessary JavaScript validation
        // The form will submit directly to the backend for validation
    </script>
    </body>
    <footer>
        <p>&copy; 2024 Kumi - All rights reserved.</p>
    </footer>    
    </html>