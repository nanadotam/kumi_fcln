<?php
// Include the database connection
include('../utils/Database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $created_by = (int)$_POST['created_by'];
    $mode = trim($_POST['mode']);
    $deadline = trim($_POST['deadline']);
    $questions = isset($_POST['questions']) ? $_POST['questions'] : [];

    // Validation: Ensure required fields are not empty
    if (empty($title) || empty($mode) || empty($created_by)) {
        die('Error: Title, mode, and created_by are required.');
    }

    // Insert the quiz details into the Quizzes table
    $insertQuizQuery = "
        INSERT INTO Quizzes (title, description, created_by, mode, deadline)
        VALUES (?, ?, ?, ?, ?)
    ";
    $stmt = $connection->prepare($insertQuizQuery);
    $stmt->bind_param('ssiss', $title, $description, $created_by, $mode, $deadline);
    
    if ($stmt->execute()) {
        $quiz_id = $stmt->insert_id; // Get the auto-generated quiz_id

        // Process each question associated with this quiz
        foreach ($questions as $question) {
            $question_text = trim($question['text']);
            $type = trim($question['type']);
            $points = isset($question['points']) ? (float)$question['points'] : 1.00;
            $answers = isset($question['answers']) ? $question['answers'] : [];

            // Insert each question into the Questions table
            $insertQuestionQuery = "
                INSERT INTO Questions (quiz_id, question_text, type, points)
                VALUES (?, ?, ?, ?)
            ";
            $questionStmt = $connection->prepare($insertQuestionQuery);
            $questionStmt->bind_param('issd', $quiz_id, $question_text, $type, $points);

            if ($questionStmt->execute()) {
                $question_id = $questionStmt->insert_id; // Get the question ID
                
                // Process answers for this question
                foreach ($answers as $answer) {
                    $answer_text = trim($answer['text']);
                    $is_correct = isset($answer['is_correct']) ? (int)$answer['is_correct'] : 0;

                    // Insert answers into the Answers table
                    $insertAnswerQuery = "
                        INSERT INTO Answers (question_id, answer_text, is_correct)
                        VALUES (?, ?, ?)
                    ";
                    $answerStmt = $connection->prepare($insertAnswerQuery);
                    $answerStmt->bind_param('isi', $question_id, $answer_text, $is_correct);
                    $answerStmt->execute();
                }
            }
        }

        echo 'Quiz and questions saved successfully.';
    } else {
        echo 'Error saving the quiz: ' . $stmt->error;
    }

    // Close statements
    $stmt->close();
    $connection->close();
} else {
    echo 'Invalid request method.';
}
?>
