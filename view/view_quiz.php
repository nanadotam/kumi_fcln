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
    <link rel="stylesheet" href="../assets/css/quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main class="quiz-page">
        <div class="quiz-view">
            <div class="page-header">
                <a href="quiz.php" class="back-btn">
                    <i class='bx bx-arrow-back'></i> Back to Quizzes
                </a>
            </div>

            <div class="quiz-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <div class="quiz-meta">
                    <p><i class='bx bx-calendar'></i> Created: <?= date('M d, Y', strtotime($quiz['created_at'])) ?></p>
                    <p><i class='bx bx-user'></i> Created by: <?= htmlspecialchars($quiz['teacher_name']) ?></p>
                    <?php if ($_SESSION['role'] === 'teacher'): ?>
                        <p><i class='bx bx-code-alt'></i> Code: <?= htmlspecialchars($quiz['quiz_code']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($quiz['description'])): ?>
                <div class="quiz-description">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="quiz-content">
                <h2>Questions</h2>
                <?php if (empty($questions)): ?>
                    <p class="no-questions">No questions have been added to this quiz yet.</p>
                <?php else: ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-card">
                            <h3>Question <?= $index + 1 ?></h3>
                            <p class="question-text"><?= htmlspecialchars($question['question_text']) ?></p>
                            
                            <div class="options-list">
                                <?php 
                                $options = json_decode($question['options'], true);
                                foreach ($options as $optionIndex => $option): 
                                ?>
                                    <div class="option">
                                        <input 
                                            type="radio" 
                                            id="q<?= $index ?>_opt<?= $optionIndex ?>" 
                                            name="q<?= $index ?>" 
                                            disabled
                                        >
                                        <label for="q<?= $index ?>_opt<?= $optionIndex ?>">
                                            <?= htmlspecialchars($option) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="quiz-actions">
                <?php if ($_SESSION['role'] === 'teacher'): ?>
                    <button onclick="editQuiz(<?= $quizId ?>)" class="edit-btn">
                        <i class='bx bxs-edit'></i> Edit Quiz
                    </button>
                <?php else: ?>
                    <button onclick="takeQuiz(<?= $quizId ?>)" class="take-btn">
                        <i class='bx bx-play'></i> Start Quiz
                    </button>
                <?php endif; ?>
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