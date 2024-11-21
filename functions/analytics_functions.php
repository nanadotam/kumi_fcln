<?php
require_once '../utils/Database.php';

function getQuizAnalytics($quizId) {
    $db = new Database();
    
    // Get overall statistics
    $sql = "SELECT 
                COUNT(*) as total_attempts,
                AVG(score) as average_score,
                MAX(score) as highest_score,
                MIN(score) as lowest_score
            FROM QuizResults 
            WHERE quiz_id = ?";
            
    $result = $db->query($sql, [$quizId]);
    $stats = $result->fetch_assoc();
    
    // Get question-specific statistics
    $sql = "SELECT 
                q.question_id,
                q.question_text,
                COUNT(CASE WHEN r.is_correct = 1 THEN 1 END) as correct_count,
                COUNT(*) as total_attempts
            FROM Questions q
            LEFT JOIN Responses r ON q.question_id = r.question_id
            WHERE q.quiz_id = ?
            GROUP BY q.question_id";
            
    $result = $db->query($sql, [$quizId]);
    
    $questionStats = [];
    while ($row = $result->fetch_assoc()) {
        $questionStats[] = [
            'question_id' => (int)$row['question_id'],
            'question_text' => htmlspecialchars($row['question_text']),
            'correct_percentage' => ($row['total_attempts'] > 0) ? 
                ($row['correct_count'] / $row['total_attempts']) * 100 : 0
        ];
    }
    
    return [
        'overall_stats' => $stats,
        'question_stats' => $questionStats
    ];
}

function getDetailedQuizAnalytics($quizId) {
    $db = new Database();
    
    // Get performance by student groups
    $sql = "SELECT 
                g.group_name,
                COUNT(DISTINCT qr.user_id) as student_count,
                AVG(qr.score) as average_score,
                MAX(qr.score) as highest_score
            FROM QuizResults qr
            JOIN GroupMembers gm ON qr.user_id = gm.user_id
            JOIN Groups g ON gm.group_id = g.group_id
            WHERE qr.quiz_id = ?
            GROUP BY g.group_id";
            
    $result = $db->query($sql, [$quizId]);
    $groupStats = [];
    while ($row = $result->fetch_assoc()) {
        $groupStats[] = [
            'group_name' => htmlspecialchars($row['group_name']),
            'student_count' => (int)$row['student_count'],
            'average_score' => (float)$row['average_score'],
            'highest_score' => (float)$row['highest_score']
        ];
    }
    
    // Get time-based analytics
    $sql = "SELECT 
                DATE(submitted_at) as submission_date,
                COUNT(*) as submission_count,
                AVG(score) as daily_average
            FROM QuizResults
            WHERE quiz_id = ?
            GROUP BY DATE(submitted_at)
            ORDER BY submission_date";
            
    $result = $db->query($sql, [$quizId]);
    $timeStats = [];
    while ($row = $result->fetch_assoc()) {
        $timeStats[] = [
            'date' => $row['submission_date'],
            'count' => (int)$row['submission_count'],
            'average' => (float)$row['daily_average']
        ];
    }
    
    return [
        'group_stats' => $groupStats,
        'time_stats' => $timeStats
    ];
} 