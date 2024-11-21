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
        <form id= "loginForm" action="Login" method="Post">
            <input type="text" id="username" name="username" required placeholder="Enter your email / username ">
            <span style= "color: tomato" id="usernameError" class="error"></span>
    
            <input type="password" id="password" name="password" required placeholder = "Enter your password">
            <span style = "color: tomato" id="passwordError" class="error"></span>
    
            <input type="submit" value="Login">
        </form>

    </div>
    <script>
            document.getElementById("loginForm").addEventListener("submit", function(event) {
                // Prevent form submission
                event.preventDefault();
                
                // Clear error messages
                document.getElementById("usernameError").textContent = "";
                document.getElementById("passwordError").textContent = "";
    
                // Get input values
                const username = document.getElementById("username").value;
                const password = document.getElementById("password").value;
    
                // Validation flags
                let valid = true;
    
                // Validate username (email format)
                if (!validateEmail(username)) {
                    document.getElementById("usernameError").textContent = "Please enter a valid email address.";
                    valid = false;
                }
    
                // Validate password
                if (!validatePassword(password)) {
                    document.getElementById("passwordError").textContent = 
                        "Password must be at least 8 characters long, include one uppercase letter, at least three digits, and one special character.";
                    valid = false;
                }
    
                // If all fields are valid, display a success message (instead of submitting)
                if (valid) {
                    alert("Form is valid. Proceeding without backend submission for now.");
                }
            });
    
            // Email validation function
            function validateEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }
    
            // Password validation function
            function validatePassword(password) {
                const passwordPattern = /^(?=.*[A-Z])(?=.*\d{3,})(?=.*[!@#\$%\^\&*\)\(+=._-]).{8,}$/;
                return passwordPattern.test(password);
            }
    
        </script>
    </body>
    <footer>
        <p>&copy; 2024 Kumi - All rights reserved.</p>
    </footer>    
    </html>