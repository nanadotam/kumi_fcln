<?php
require_once '../db/config.php';
// header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'create':
                createUser($connection, $_POST);
                break;

            case 'update':
                updateUser($connection, $_POST);
                break;

            case 'delete':
                deleteUser($connection, $_POST);
                break;
                
            default:
                throw new Exception("Invalid action.");
        }

        // Respond with success
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => ucfirst($action) . ' operation completed successfully.']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Creates a new user.
 */
function createUser($connection, $data) {
    $requiredFields = ['fname', 'lname', 'email', 'password', 'role'];
    validateFields($data, $requiredFields);

    $fname = $data['fname'];
    $lname = $data['lname'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = (int)$data['role'];

    $stmt = $connection->prepare("INSERT INTO users (fname, lname, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    if (!$stmt) {
        throw new Exception("Failed to prepare the statement.");
    }
    $stmt->bind_param("ssssi", $fname, $lname, $email, $password, $role);
    $stmt->execute();
    $stmt->close();
}

/**
 * Updates an existing user.
 */
function updateUser($connection, $data) {
    $requiredFields = ['user_id', 'fname', 'lname', 'email', 'role'];
    validateFields($data, $requiredFields);

    $userId = (int)$data['user_id'];
    $fname = $data['fname'];
    $lname = $data['lname'];
    $email = $data['email'];
    $role = (int)$data['role'];

    $stmt = $connection->prepare("UPDATE users SET fname = ?, lname = ?, email = ?, role = ?, updated_at = NOW() WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare the statement.");
    }
    $stmt->bind_param("sssii", $fname, $lname, $email, $role, $userId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Deletes a user.
 */
function deleteUser($connection, $data) {
    $requiredFields = ['user_id'];
    validateFields($data, $requiredFields);

    $userId = (int)$data['user_id'];

    $stmt = $connection->prepare("DELETE FROM users WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare the statement.");
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Validates required fields in the data array.
 */
function validateFields($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("The field '$field' is required.");
        }
    }
}
