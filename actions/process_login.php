<?php
session_start();
require_once '../utils/Database.php';

function processLogin($email, $password) {
    $db = Database::getInstance();
    
    $sql = "SELECT user_id, role, password_hash FROM Users WHERE email = ?";
    $result = $db->query($sql, [$email]);
    
    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            
            return true;
        }
    }
    
    return false;
}

// Usage in login form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (processLogin($email, $password)) {
        header('Location: /dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header('Location: /login.php');
        exit();
    }
} 