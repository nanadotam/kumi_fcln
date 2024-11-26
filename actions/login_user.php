<?php
// Initialize session and include database configuration
session_start();
require_once '../db/config.php';

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received.");
    // Get and sanitize user input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs (add your validation logic here)

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
            // Handle invalid password
            error_log("Invalid password.");
            $_SESSION['login_error'] = "Invalid email or password. Please try again.";
            header("Location: ../view/login.php");
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        // Handle non-existent user
        error_log("No user found with that email.");
        $_SESSION['login_error'] = "Invalid email or password. Please try again.";
        header("Location: ../view/login.php");
        $stmt->close();
        $conn->close();
        exit();
    }
}


?>
