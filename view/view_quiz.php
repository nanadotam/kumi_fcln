<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
if (!$quizId) {
    header('Location: quiz.php');
    exit();
}

// Get quiz details
$quiz = getQuizById($quizId);
if (!$quiz) {
    $_SESSION['error'] = "Quiz not found or database error occurred";
    header('Location: quiz.php');
    exit();
}

// Get quiz questions
$questions = getQuizQuestions($quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?> - Kumi</title>
    <link rel="stylesheet" href="../assets/css/view_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main>
        <div class="quiz-wrapper">
            <a href="quiz.php" class="back-link">
                <i class='bx bx-arrow-back'></i> Back to Quizzes
            </a>

            <div class="quiz-container">
                <div class="quiz-header">
                    <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                    <div class="quiz-metadata">
                        <span><i class='bx bx-calendar'></i> Created: <?= date('M d, Y', strtotime($quiz['created_at'])) ?></span>
                        <span><i class='bx bx-user'></i> Created by: <?= htmlspecialchars($quiz['teacher_name']) ?></span>
                        <?php if ($_SESSION['role'] === 'teacher'): ?>
                            <span><i class='bx bx-code-alt'></i> Code: <?= htmlspecialchars($quiz['quiz_code']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($quiz['description'])): ?>
                    <div class="description">
                        <h2>Description</h2>
                        <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="questions-section">
                    <h2 class="questions-header">Questions</h2>
                    <?php if (empty($questions)): ?>
                        <div class="no-questions-message">
                            No questions have been added to this quiz yet.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="quiz-actions">
                    <?php if ($_SESSION['role'] === 'teacher'): ?>
                        <button onclick="editQuiz(<?= $quizId ?>)" class="edit-quiz-btn">
                            <i class='bx bxs-edit'></i> Edit Quiz
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
    function editQuiz(quizId) {
        window.location.href = `edit_quiz.php?id=${quizId}`;
    }

    function takeQuiz(quizId) {
        window.location.href = `take_quiz.php?id=${quizId}`;
    }
    </script>
</body>
</html> 
</html> 