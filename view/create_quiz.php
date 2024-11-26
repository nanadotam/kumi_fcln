<?php
session_start();

function generateCode($length = 6) {
    // Generate a unique ID using uniqid
    $uniqueId = uniqid(mt_rand(), true);

    // Remove any non-alphanumeric characters
    $cleanId = preg_replace('/[^a-zA-Z0-9]/', '', $uniqueId);

    // Shuffle the characters to add randomness
    $shuffled = str_shuffle($cleanId);

    // Return the desired length
    return substr($shuffled, 0, $length);
}

// Add this line to ensure JSON response
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kumidb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Please log in first"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Create a prepared statement
    $stmt = $conn->prepare("INSERT INTO Quizzes (title, description, created_by, mode, deadline, 
            shuffle_questions, shuffle_answers, max_attempts, time_limit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("ssissiiis", $title, $description, $created_by, $mode, $deadline, 
                      $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit);
    
    // Execute the statement
    if ($stmt->execute()) {
        $quiz_id = $conn->insert_id;

        // Generate a unique quiz code
        do {
            $quiz_code = generateCode(6);
            // Check if code already exists
            $check_stmt = $conn->prepare("SELECT quiz_id FROM Quizzes WHERE quiz_code = ?");
            $check_stmt->bind_param("s", $quiz_code);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $is_unique = ($result->num_rows === 0);
            $check_stmt->close();
        } while (!$is_unique);

        // Update the quiz with the unique code
        $code_stmt = $conn->prepare("UPDATE Quizzes SET quiz_code = ? WHERE quiz_id = ?");
        $code_stmt->bind_param("si", $quiz_code, $quiz_id);
        $code_stmt->execute();
        $code_stmt->close();

        // Insert questions and answers
        foreach ($_POST['questions'] as $question) {
            $question_text = $question['question_text'];
            $type = $question['type'];
            $points = $question['points'];
            $model_answer = isset($question['model_answer']) ? $question['model_answer'] : NULL;
            $order_position = NULL;

            // Insert question using prepared statement
            $stmt = $conn->prepare("INSERT INTO Questions (quiz_id, question_text, type, points, model_answer, order_position) 
                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issisi", $quiz_id, $question_text, $type, $points, $model_answer, $order_position);
            
            if ($stmt->execute()) {
                $question_id = $conn->insert_id;
                $stmt->close();

                // Handle true/false questions differently
                if ($type === 'true_false') {
                    // Insert True option
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, 'True', ?, ?)");
                    $true_is_correct = isset($question['answers']['is_correct']) && $question['answers']['is_correct'] == '1' ? 1 : 0;
                    $order_position = 1;
                    $stmt_answer->bind_param("iii", $question_id, $true_is_correct, $order_position);
                    $stmt_answer->execute();
                    $stmt_answer->close();

                    // Insert False option
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, 'False', ?, ?)");
                    $false_is_correct = $true_is_correct ? 0 : 1;
                    $order_position = 2;
                    $stmt_answer->bind_param("iii", $question_id, $false_is_correct, $order_position);
                    $stmt_answer->execute();
                    $stmt_answer->close();
                } else {
                    // Handle other question types as before
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, ?, ?, ?)");
                            
                    foreach ($question['answers'] as $index => $answer) {
                        $answer_text = $answer['answer_text'];
                        $is_correct = isset($answer['is_correct']) && $answer['is_correct'] == 1 ? 1 : 0;
                        $order_position = $index + 1;

                        $stmt_answer->bind_param("isii", $question_id, $answer_text, $is_correct, $order_position);
                        $stmt_answer->execute();
                    }
                    
                    $stmt_answer->close();
                }
            } else {
                $stmt->close();
            }
        }
        echo json_encode([
            'success' => true,
            'message' => 'Quiz created successfully! Quiz Code: ' . $quiz_code
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $stmt->error
        ]);
    }
    
    // $stmt->close();
}

$conn->close();

// Remove or comment out all HTML output
// The rest of your HTML code should be in a separate file
?>

