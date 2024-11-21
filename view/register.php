<?php
include '../db/config.php';
?>

<!DOCTYPE html>
<html>
<head>
	<link rel = "icon" type = "image/x-icon" href = "mainLogo.jpg">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../assets/css/styles.css">
	<link rel="stylesheet" href="../assets/css/register.css">
	<Title> Kumi-Register</Title>
</head>
<body>
<div class = "container">
    <header>
        <nav class="navbar">
          <div class="logo">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
          </div>
          <div class="auth-buttons">
            <a href="../view/index.php" class="btn btn-login">Home</a>
            <a href="../view/login.php" class="btn btn-register">Login</a>
          </div>
        </nav>
      </header>

    <?php if (isset($_SESSION['notification'])): ?>
        <div class="notification">
            <?php echo $_SESSION['notification']; unset($_SESSION['notification']); ?>
        </div>
    <?php endif; ?>

	<div class="signup-container">
	
        <h2>Register</h2>
		<form id="signupForm" method="POST" action="../actions/register_user.php">
			<input type="text" id="firstName" name="firstName" required placeholder="First Name">
			<span class="error" id="firstNameError"></span>

			<input type="text" id="lastName" name="lastName" required placeholder="Last Name">
			<span class="error" id="lastNameError"></span>

			<input type="email" id="email" name="email" required placeholder="Enter your Email">
			<span class="error" id="emailError"></span>

			<input type="password" id="password" name="password" required placeholder="Enter your password">
			<span class="error" id="passwordError"></span>

			<input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm your password">
			<span class="error" id="confirmPasswordError"></span>

			<select id="role" name="role" required>
				<option value="">Select Role</option>
				<option value="student">Student</option>
				<option value="teacher">Teacher</option>
			</select>
			<span class="error" id="roleError"></span>

			<input type="submit" value="Sign Up">
		</form>
	</div>
</div>

<script src="../assets/js/register.js"></script>
</body>
 <footer>
    <p>&copy; 2024 Kumi - All rights reserved.</p>
</footer>
    
</html>