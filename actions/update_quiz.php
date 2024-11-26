<?php
session_start();
require_once '../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

$quiz_id = $_POST['quiz_id'];
$title = $_POST['title'];
$description = $_POST['description'];
$mode = $_POST['mode'];
$deadline = $_POST['deadline'];
$shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
$shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
$max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
$time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

try {
    $conn->begin_transaction();

    // Update quiz details
    $stmt = $conn->prepare("UPDATE Quizzes SET title = ?, description = ?, mode = ?, 
            deadline = ?, shuffle_questions = ?, shuffle_answers = ?, 
            max_attempts = ?, time_limit = ? WHERE quiz_id = ?");
    
    $stmt->bind_param("ssssiiisi", $title, $description, $mode, $deadline,
            $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit, $quiz_id);
    
    $stmt->execute();

    // Delete existing questions and answers
    $stmt = $conn->prepare("DELETE FROM Questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Insert updated questions and answers
    foreach ($_POST['questions'] as $question) {
        // Insert question
        $stmt = $conn->prepare("INSERT INTO Questions (quiz_id, question_text, type, points, 
                model_answer, order_position) VALUES (?, ?, ?, ?, ?, ?)");
        // ... bind parameters and execute
        
        $question_id = $conn->insert_id;
        
        // Insert answers
        foreach ($question['answers'] as $answer) {
            $stmt = $conn->prepare("INSERT INTO Answers (question_id, answer_text, 
                    is_correct, order_position) VALUES (?, ?, ?, ?)");
            // ... bind parameters and execute
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Quiz updated successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close(); 