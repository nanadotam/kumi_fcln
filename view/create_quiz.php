<?php
session_start();
require_once '../utils/Database.php';

function generateCode($length = 6) {
    $uniqueId = uniqid(mt_rand(), true);
    $cleanId = preg_replace('/[^a-zA-Z0-9]/', '', $uniqueId);
    $shuffled = str_shuffle($cleanId);
    return substr($shuffled, 0, $length);
}

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => "Please log in first"]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Begin transaction
        $db->begin_transaction();
        
        // Insert quiz data
        $title = $_POST['title'];
        $description = $_POST['description'];
        $created_by = $_SESSION['user_id'];
        $mode = $_POST['mode'];
        $deadline = $_POST['deadline'];
        $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
        $shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
        $max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
        $time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

        $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline, 
                shuffle_questions, shuffle_answers, max_attempts, time_limit) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $db->query($sql, [
            $title, $description, $created_by, $mode, $deadline,
            $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit
        ]);
        
        $quiz_id = $db->lastInsertId();

        // Generate unique quiz code
        do {
            $quiz_code = generateCode(6);
            $result = $db->query("SELECT quiz_id FROM Quizzes WHERE quiz_code = ?", [$quiz_code]);
            $is_unique = ($result->num_rows === 0);
        } while (!$is_unique);

        // Update quiz with code
        $db->query("UPDATE Quizzes SET quiz_code = ? WHERE quiz_id = ?", [$quiz_code, $quiz_id]);

        // Insert questions and answers
        foreach ($_POST['questions'] as $question) {
            $sql = "INSERT INTO Questions (quiz_id, question_text, type, points, model_answer, order_position) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $quiz_id,
                $question['question_text'],
                $question['type'],
                $question['points'],
                isset($question['model_answer']) ? $question['model_answer'] : NULL,
                NULL
            ]);
            
            $question_id = $db->lastInsertId();

            // Handle different question types
            if ($question['type'] === 'true_false') {
                // Insert True option
                $true_is_correct = isset($question['answers']['is_correct']) && $question['answers']['is_correct'] == '1' ? 1 : 0;
                $db->query(
                    "INSERT INTO Answers (question_id, answer_text, is_correct, order_position) VALUES (?, 'True', ?, ?)",
                    [$question_id, $true_is_correct, 1]
                );

                // Insert False option
                $false_is_correct = $true_is_correct ? 0 : 1;
                $db->query(
                    "INSERT INTO Answers (question_id, answer_text, is_correct, order_position) VALUES (?, 'False', ?, ?)",
                    [$question_id, $false_is_correct, 2]
                );
                
            } elseif ($question['type'] === 'short_answer') {
                $model_answer = $question['model_answer'] ?? '';
                $db->query(
                    "INSERT INTO Answers (question_id, answer_text, is_correct, order_position) VALUES (?, ?, 1, 1)",
                    [$question_id, $model_answer]
                );
                
            } else {
                // Handle multiple choice and multiple answer questions
                foreach ($question['answers'] as $index => $answer) {
                    $is_correct = isset($answer['is_correct']) && $answer['is_correct'] == 1 ? 1 : 0;
                    $db->query(
                        "INSERT INTO Answers (question_id, answer_text, is_correct, order_position) VALUES (?, ?, ?, ?)",
                        [$question_id, $answer['answer_text'], $is_correct, $index + 1]
                    );
                }
            }
        }
        
        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Quiz created successfully! Quiz Code: ' . $quiz_code
        ]);
    }
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

