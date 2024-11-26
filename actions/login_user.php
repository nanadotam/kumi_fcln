<?php
// Initialize session and include database configuration
session_start();
require_once '../db/config.php';

// Maximum login attempts allowed
define('MAX_LOGIN_ATTEMPTS', 3);
// Lockout time in seconds (e.g., 15 minutes = 900 seconds)
define('LOCKOUT_TIME', 900);

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received.");
    // Get and sanitize user input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if the IP is currently locked out
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $result = $stmt->get_result();
    $attempts = $result->fetch_assoc();
    
    // Check if user is locked out
    if ($attempts && $attempts['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $lockout_time_remaining = LOCKOUT_TIME - (time() - strtotime($attempts['last_attempt']));
        
        if ($lockout_time_remaining > 0) {
            $_SESSION['login_error'] = "Too many failed attempts. Please try again in " . 
                ceil($lockout_time_remaining / 60) . " minutes.";
            header("Location: ../view/login.php");
            exit();
        } else {
            // Reset attempts after lockout period
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();
        }
    }

    // Prepare and execute query to check if user exists
    $stmt = $conn->prepare("SELECT user_id, role, password, first_name FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email); // This line binds the email parameter to the prepared statement as a string.
    $stmt->execute(); 
    $stmt->store_result();
    error_log("Query executed, number of rows: " . $stmt->num_rows);

    // If user exists in database
    if ($stmt->num_rows > 0) {
        // Bind the query results to variables BEFORE using them
        $stmt->bind_result($userId, $role, $hashedPassword, $firstName);
        $stmt->fetch();
        
        error_log("User found: ID = $userId, Role = $role");

        // Check if provided password matches stored hash
        if (password_verify($password, $hashedPassword)) {
            error_log("Password verified.");
            // Successful login - reset attempts
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();
            
            // Store user info in session
            $_SESSION['user_id'] = $userId;
            $_SESSION['role'] = $role;
            $_SESSION['first_name'] = $firstName;

            // Redirect user based on their role
            if ($role === 'student') {
                header("Location: ../view/student_dashboard.php");
                exit();
            } elseif ($role === 'teacher') {
                header("Location: ../view/teacher_dashboard.php");
                exit();
            } else {
                // Default redirect if role is not recognized
                header("Location: ../view/index.php");
                exit();
            }
        } else {
            // Failed login - record attempt
            recordFailedAttempt($conn, $ip_address);
        }
    } else {
        // Failed login - record attempt
        recordFailedAttempt($conn, $ip_address);
    }
    
    $_SESSION['login_error'] = "Invalid email or password. Please try again.";
    header("Location: ../view/login.php");
    exit();
}

function recordFailedAttempt($conn, $ip_address) {
    $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                           VALUES (?, 1, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           attempts = attempts + 1, 
                           last_attempt = NOW()");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
}

?>
