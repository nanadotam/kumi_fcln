<?php
session_start();
require_once '../functions/auth_functions.php';

// Ensure only teachers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/create_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="quiz-creator">
        <div class="quiz-header">
            <input type="text" id="quizTitle" class="quiz-title" placeholder="Untitled Quiz">
            <input type="text" id="quizDescription" class="quiz-description" placeholder="Quiz description">
            
            <div class="quiz-settings">
                <div class="setting-group">
                    <label>Due Date:</label>
                    <input type="datetime-local" id="quizDueDate">
                </div>
            </div>
        </div>

        <div id="questionsContainer">
            <!-- Questions will be added here dynamically -->
        </div>

        <div class="action-buttons">
            <button id="addQuestionBtn" class="btn-add">
                <i class='bx bx-plus'></i> Add Question
            </button>
            <button id="saveQuizBtn" class="btn-save">
                <i class='bx bx-save'></i> Save Quiz
            </button>
        </div>
    </div>

    <script src="../assets/js/quiz.js"></script>
</body>
</html>