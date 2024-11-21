<?php
require_once '../utils/Database.php';

function getUserProfile($userId) {
    $db = new Database();
    
    $sql = "SELECT u.*, 
            COUNT(DISTINCT qr.quiz_id) as quizzes_taken,
            AVG(qr.score) as average_score
            FROM Users u
            LEFT JOIN QuizResults qr ON u.user_id = qr.user_id
            WHERE u.user_id = ?
            GROUP BY u.user_id";
            
    $result = $db->query($sql, [$userId]);
    
    if ($row = $result->fetch_assoc()) {
        return [
            'user_id' => (int)$row['user_id'],
            'first_name' => htmlspecialchars($row['first_name']),
            'last_name' => htmlspecialchars($row['last_name']),
            'email' => htmlspecialchars($row['email']),
            'role' => $row['role'],
            'quizzes_taken' => (int)$row['quizzes_taken'],
            'average_score' => (float)$row['average_score'],
            'groups' => getUserGroups($userId)
        ];
    }
    
    return null;
}

function updateUserProfile($userId, $data) {
    $db = new Database();
    
    $sql = "UPDATE Users 
            SET first_name = ?, last_name = ?, email = ? 
            WHERE user_id = ?";
            
    return $db->query($sql, [
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $userId
    ]);
} 