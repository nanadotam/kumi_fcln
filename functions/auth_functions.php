<?php
// Function to validate registration data
function validateRegistrationData($email, $password, $first_name, $last_name) {
    $errors = [];
    
    if (empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        $errors[] = 'All fields are required.';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    
    // Password complexity check
    if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/", $password)) {
        $errors[] = 'Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    // Validate names
    if (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
        $errors[] = 'First name must only contain alphabets.';
    }
    if (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $errors[] = 'Last name must only contain alphabets.';
    }
    
    return $errors;
}

// Function to check if email exists
function checkEmailExists($conn, $email) {
    try {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Database error: Failed to prepare statement. " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    } catch (Exception $e) {
        error_log("Error checking email: " . $e->getMessage());
        return false; // Handle gracefully in calling code
    }
}

// Function to register user
function registerUser($conn, $first_name, $last_name, $email, $password, $role) {
    try {
        // Inline role validation
        if (!in_array($role, ['student', 'teacher'])) {
            return [
                'success' => false,
                'message' => 'Invalid role selected.'
            ];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $currentTime = date('Y-m-d H:i:s');
        
        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Database error: Failed to prepare statement. " . $conn->error);
        }
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $hashedPassword, $role, $currentTime, $currentTime);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: Failed to execute statement. " . $stmt->error);
        }
        
        return [
            'success' => true,
            'message' => 'Registration successful!'
        ];
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'A server error occurred. Please try again later.'
        ];
    }
}
?>
