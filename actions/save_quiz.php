<?php
session_start();
include('../utils/Database.php');

// Set headers for JSON response
header('Content-Type: application/json');

// Verify user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    // Get JSON input
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    $db = Database::getInstance();
    $db->begin_transaction();

    // Insert quiz
    $insertQuizQuery = "
        INSERT INTO Quizzes (title, description, created_by, mode, deadline, quiz_code)
        VALUES (?, ?, ?, 'asynchronous', ?, ?, ?)
    ";
    
    $quizResult = $db->query($insertQuizQuery, [
        $data['title'],
        $data['description'],
        $_SESSION['user_id'],
        $data['dueDate'],
        $data['quiz_code']
    ]);

    $quiz_id = $db->insert_id();

    // Process questions
    foreach ($data['questions'] as $question) {
        // Convert frontend question type to database type
        $questionType = match($question['type']) {
            'paragraph' => 'short_answer',
            'multiple_choice', 'checkbox' => 'multiple_choice',
            default => 'multiple_choice'
        };

        // Insert question
        $insertQuestionQuery = "
            INSERT INTO Questions (quiz_id, question_text, type, points)
            VALUES (?, ?, ?, 1.00)
        ";
        
        $db->query($insertQuestionQuery, [
            $quiz_id,
            $question['text'],
            $questionType
        ]);

        $question_id = $db->insert_id();

        // Insert options/answers if not a paragraph question
        if ($question['type'] !== 'paragraph' && !empty($question['options'])) {
            foreach ($question['options'] as $index => $optionText) {
                $insertAnswerQuery = "
                    INSERT INTO Answers (question_id, answer_text, is_correct)
                    VALUES (?, ?, 0)
                ";
                
                $db->query($insertAnswerQuery, [
                    $question_id,
                    $optionText
                ]);
            }
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Quiz saved successfully'
    ]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>