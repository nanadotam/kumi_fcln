<?php
session_start();
header('Content-Type: application/json');
require_once '../utils/Database.php';
require_once '../functions/quiz_functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log("Quiz submission started");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Debug incoming data
    error_log("POST data: " . print_r($_POST, true));
    error_log("Session data: " . print_r($_SESSION, true));

    // Validate required data
    if (!isset($_POST['quiz_id'])) {
        throw new Exception('Missing quiz_id');
    }
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    if (!isset($_POST['responses']) || empty($_POST['responses'])) {
        throw new Exception('No quiz responses provided');
    }

    $quizId = $_POST['quiz_id'];
    $userId = $_SESSION['user_id'];
    $responses = $_POST['responses'];
    
    // Debug validated data
    error_log("Quiz ID: $quizId");
    error_log("User ID: $userId");
    error_log("Responses: " . print_r($responses, true));
    
    $db = Database::getInstance();
    $db->begin_transaction();
    
    // Create quiz result
    $sql = "INSERT INTO QuizResults (quiz_id, user_id, score, submitted_at) 
            VALUES (?, ?, 0, NOW())";
    $result = $db->query($sql, [$quizId, $userId]);
    
    $resultId = $db->insert_id();
    error_log("Created result ID: $resultId");
    
    $totalScore = 0;
    $totalPoints = 0;
    
    // Process each response
    foreach ($responses as $questionId => $answerId) {
        error_log("Processing question $questionId with answer $answerId");
        
        // Validate the answer using the Answers table
        $sql = "SELECT a.is_correct, q.points 
                FROM Answers a
                JOIN Questions q ON a.question_id = q.question_id
                WHERE a.question_id = ? AND a.answer_id = ?";
        $result = $db->query($sql, [$questionId, $answerId]);
        $answer = $result->fetch_assoc();
        
        if (!$answer) {
            throw new Exception("Invalid question or answer ID: $questionId, $answerId");
        }
        
        $isCorrect = (bool)$answer['is_correct'];
        $points = $answer['points'] ?? 1;
        $totalPoints += $points;
        
        if ($isCorrect) {
            $totalScore += $points;
        }
        
        // Store the response
        $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct) 
                VALUES (?, ?, ?, ?)";
        $db->query($sql, [$resultId, $questionId, $answerId, $isCorrect ? 1 : 0]);
    }
    
    // Calculate percentage score
    $percentageScore = ($totalPoints > 0) ? ($totalScore / $totalPoints) * 100 : 0;
    
    // Update final score
    $sql = "UPDATE QuizResults SET score = ? WHERE result_id = ?";
    $db->query($sql, [$percentageScore, $resultId]);
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'result_id' => $resultId,
        'score' => $percentageScore,
        'message' => 'Quiz submitted successfully!'
    ]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    
    error_log("Quiz submission error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit quiz: ' . $e->getMessage()
    ]);
} 