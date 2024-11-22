<?php
require_once '../utils/Database.php';

function getStudentProgress($studentId) {
    $db = new Database();
    
    $sql = "SELECT 
            COUNT(DISTINCT q.quiz_id) as total_quizzes,
            COUNT(DISTINCT qr.quiz_id) as completed_quizzes,
            AVG(qr.score) as average_score,
            SUM(CASE WHEN qr.score >= 70 THEN 1 ELSE 0 END) as quizzes_passed
            FROM Quizzes q
            LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id AND qr.user_id = ?
            WHERE q.deadline >= CURDATE() OR q.deadline IS NULL";
            
    $result = $db->query($sql, [$studentId]);
    return $result->fetch_assoc();
}

function getUpcomingDeadlines($studentId) {
    $db = new Database();
    
    $sql = "SELECT q.quiz_id, q.title, q.deadline
            FROM Quizzes q
            LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id AND qr.user_id = ?
            WHERE qr.result_id IS NULL 
            AND q.deadline IS NOT NULL 
            AND q.deadline > NOW()
            ORDER BY q.deadline ASC
            LIMIT 5";
            
    $result = $db->query($sql, [$studentId]);
    
    $deadlines = [];
    while ($row = $result->fetch_assoc()) {
        $deadlines[] = [
            'quiz_id' => (int)$row['quiz_id'],
            'title' => htmlspecialchars($row['title']),
            'deadline' => $row['deadline']
        ];
    }
    
    return $deadlines;
}

function createNotification($userId, $type, $message, $relatedId = null) {
    $db = new Database();
    
    $sql = "INSERT INTO Notifications (user_id, type, message, related_id) 
            VALUES (?, ?, ?, ?)";
            
    return $db->query($sql, [$userId, $type, $message, $relatedId]);
}

function getUserNotifications($userId, $limit = 10) {
    $db = new Database();
    
    $sql = "SELECT * FROM Notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?";
            
    $result = $db->query($sql, [$userId, $limit]);
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'id' => (int)$row['notification_id'],
            'type' => $row['type'],
            'message' => htmlspecialchars($row['message']),
            'read' => (bool)$row['is_read'],
            'created_at' => $row['created_at']
        ];
    }
    
    return $notifications;
}

function markNotificationAsRead($notificationId) {
    $db = new Database();
    
    $sql = "UPDATE Notifications SET is_read = 1 WHERE notification_id = ?";
    return $db->query($sql, [$notificationId]);
} 

