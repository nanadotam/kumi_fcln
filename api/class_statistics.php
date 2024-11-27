<?php
require_once '../utils/Database.php';
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    // Calculate average score
    $avgScoreQuery = "
        SELECT COALESCE(AVG(score), 0) as average_score 
        FROM QuizResults 
        WHERE score IS NOT NULL";
    
    $avgResult = $db->query($avgScoreQuery);
    $averageScore = round($avgResult->fetch_assoc()['average_score'], 1);
    
    // Calculate participation rate
    $participationQuery = "
        SELECT 
            (COUNT(DISTINCT user_id) * 100.0 / 
            (SELECT COUNT(*) FROM Users WHERE role = 'student')) as participation_rate
        FROM QuizResults";
    
    $partResult = $db->query($participationQuery);
    $participationRate = round($partResult->fetch_assoc()['participation_rate'], 1);
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'average_score' => $averageScore,
            'participation_rate' => $participationRate
        ]
    ]);

} catch (Exception $e) {
    error_log("Error calculating class statistics: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error calculating class statistics'
    ]);
} 