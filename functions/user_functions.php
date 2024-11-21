<?php
require_once __DIR__ . '/../utils/Database.php';

function getUserData($userId) {
    $db = Database::getInstance();
    
    $query = "SELECT 
        u.user_id,
        u.first_name,
        u.last_name,
        u.email,
        u.role,
        CASE 
            WHEN u.role = 'student' THEN s.student_id
            WHEN u.role = 'teacher' THEN t.department
        END as additional_info
        FROM users u
        LEFT JOIN students s ON u.user_id = s.user_id
        LEFT JOIN teachers t ON u.user_id = t.user_id
        WHERE u.user_id = ?";
    
    $result = $db->query($query, [$userId]);
    
    if ($result && $userData = $result->fetch_assoc()) {
        if ($userData['role'] === 'student') {
            $userData['student_id'] = $userData['additional_info'];
        } else {
            $userData['department'] = $userData['additional_info'];
        }
        unset($userData['additional_info']);
        return $userData;
    }
    
    throw new Exception('User not found');
}

function updateUserProfile($userId, $updateData) {
    $db = Database::getInstance();
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Update users table
        $updates = [];
        $params = [];
        foreach ($updateData as $field => $value) {
            if ($field !== 'department') { // Handle department separately
                $updates[] = "$field = ?";
                $params[] = $value;
            }
        }
        $params[] = $userId;
        
        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
        $db->query($query, $params);
        
        // Update teacher department if applicable
        if (isset($updateData['department'])) {
            $query = "UPDATE teachers SET department = ? WHERE user_id = ?";
            $db->query($query, [$updateData['department'], $userId]);
        }
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}
