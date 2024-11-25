<?php
session_start();

/**
 * Check if the current user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the current user is a teacher
 * @return bool
 */
function isTeacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}

/**
 * Require teacher authentication
 * Redirects to login if not authenticated as teacher
 */
function requireTeacher() {
    if (!isLoggedIn() || !isTeacher()) {
        header('Location: /login.php');
        exit();
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * @return string|null
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
} 