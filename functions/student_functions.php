<?php
require_once '../utils/Database.php';

function getStudentProgress($studentId) {
    $db = Database::getInstance();
    
    $sql = "SELECT 
            COUNT(DISTINCT q.quiz_id) as total_quizzes,
            COUNT(DISTINCT qr.quiz_id) as completed_quizzes,
            AVG(qr.score) as average_score,
            SUM(CASE WHEN qr.score >= 70 THEN 1 ELSE 0 END) as quizzes_passed
            FROM Quizzes q
            LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id AND qr.user_id = ?";
            
    $result = $db->query($sql, [$studentId]);
    $stats = $result->fetch_assoc();
    
    return [
        'total_quizzes' => (int)$stats['total_quizzes'],
        'completed_quizzes' => (int)$stats['completed_quizzes'],
        'average_score' => $stats['average_score'] ? round($stats['average_score'], 1) : 0,
        'quizzes_passed' => (int)$stats['quizzes_passed']
    ];
}

function getUpcomingDeadlines($studentId) {
    $db = Database::getInstance();
    
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
            'title' => htmlspecialchars($row['title']),
            'deadline' => date('M d, Y', strtotime($row['deadline']))
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

function getUserNotifications($studentId) {
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM Notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10";
            
    $result = $db->query($sql, [$studentId]);
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'id' => (int)$row['notification_id'],
            'message' => htmlspecialchars($row['message']),
            'read' => (bool)$row['is_read'],
            'created_at' => date('M d, Y', strtotime($row['created_at']))
        ];
    }
    
    return $notifications;
}

function markNotificationAsRead($notificationId) {
    $db = new Database();
    
    $sql = "UPDATE Notifications SET is_read = 1 WHERE notification_id = ?";
    return $db->query($sql, [$notificationId]);
} 