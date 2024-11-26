<?php
session_start();
require_once '../functions/quiz_functions.php';
require_once '../functions/auth_functions.php';
require_once '../utils/Database.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();

    // Validate quiz access
    $sql = "SELECT q.*, u.role 
            FROM Quizzes q 
            JOIN Users u ON u.user_id = ?
            WHERE q.quiz_id = ? AND (
                q.mode = 'individual' OR 
                (q.mode = 'group' AND EXISTS (
                    SELECT 1 FROM GroupMembers gm 
                    WHERE gm.user_id = ? AND gm.group_id IN (
                        SELECT group_id FROM GroupMembers
                    )
                ))
            )";
    
    $result = $db->query($sql, [$userId, $quizId, $userId]);

    if ($result->num_rows === 0) {
        header('Location: dashboard.php');
        exit();
    }

    $quiz = $result->fetch_assoc();

    // Get questions and answers
    $sql = "SELECT q.*, a.answer_id, a.answer_text, a.is_correct 
            FROM Questions q
            LEFT JOIN Answers a ON q.question_id = a.question_id
            WHERE q.quiz_id = ?
            ORDER BY q.order_position, q.question_id";
            
    $result = $db->query($sql, [$quizId]);

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['question_id'];
        if (!isset($questions[$questionId])) {
            $questions[$questionId] = [
                'question_id' => $questionId,
                'text' => $row['question_text'],
                'type' => $row['type'],
                'points' => $row['points'],
                'answers' => []
            ];
        }
        if ($row['answer_id']) {
            $questions[$questionId]['answers'][] = [
                'answer_id' => $row['answer_id'],
                'text' => $row['answer_text'],
                'is_correct' => $row['is_correct']
            ];
        }
    }

    // Convert associative array to indexed array
    $quiz['questions'] = array_values($questions);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $responses = $_POST['responses'] ?? [];
        $totalScore = 0;
        $totalPoints = 0;
        
        // Start transaction
        $db->begin_transaction();
        
        try {
            // Insert quiz result
            $sql = "INSERT INTO QuizResults (quiz_id, user_id, score) VALUES (?, ?, 0)";
            $db->query($sql, [$quizId, $userId]);
            $resultId = $db->lastInsertId();
            
            // Process each response
            foreach ($quiz['questions'] as $question) {
                $questionId = $question['question_id'];
                $response = $responses["q_{$questionId}"] ?? null;
                $isCorrect = 0;
                
                if ($question['type'] === 'short_answer') {
                    $textResponse = $response;
                    $isCorrect = !empty($textResponse) ? 1 : 0;
                    
                    // Insert text response
                    $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct, text_response) VALUES (?, ?, NULL, ?, ?)";
                    $db->query($sql, [$resultId, $questionId, $isCorrect, $textResponse]);
                    
                } elseif ($question['type'] === 'multiple_answer') {
                    // Handle multiple answer questions
                    $selectedAnswers = is_array($response) ? $response : [];
                    $correctAnswers = array_filter($question['answers'], fn($a) => $a['is_correct'] == 1);
                    
                    // Check if all correct answers are selected and no incorrect answers are selected
                    $isCorrect = 1;
                    foreach ($question['answers'] as $answer) {
                        $isSelected = in_array($answer['answer_id'], $selectedAnswers);
                        if (($answer['is_correct'] && !$isSelected) || (!$answer['is_correct'] && $isSelected)) {
                            $isCorrect = 0;
                            break;
                        }
                    }
                    
                    // Insert a response for each selected answer
                    foreach ($selectedAnswers as $selectedAnswerId) {
                        $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct, text_response) VALUES (?, ?, ?, ?, NULL)";
                        $db->query($sql, [$resultId, $questionId, $selectedAnswerId, $isCorrect]);
                    }
                    
                } else {
                    // For multiple choice and true/false questions
                    $selectedAnswerId = $response ? (int)$response : null;
                    foreach ($question['answers'] as $answer) {
                        if ($answer['answer_id'] == $selectedAnswerId) {
                            $isCorrect = $answer['is_correct'];
                            break;
                        }
                    }
                    
                    $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct, text_response) VALUES (?, ?, ?, ?, NULL)";
                    $db->query($sql, [$resultId, $questionId, $selectedAnswerId, $isCorrect]);
                }
                
                if ($isCorrect) {
                    $totalScore += $question['points'];
                }
                $totalPoints += $question['points'];
            }
            
            // Update final score
            $finalScore = ($totalScore / $totalPoints) * 100;
            $sql = "UPDATE QuizResults SET score = ? WHERE result_id = ?";
            $db->query($sql, [$finalScore, $resultId]);
            
            $db->commit();
            header('Location: quiz_result.php?id=' . $resultId);
            exit();
            
        } catch (Exception $e) {
            $db->rollback();
            $error = "Error submitting quiz: " . $e->getMessage();
        }
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?> 