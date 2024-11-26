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

// Get quiz questions and their answers
$questions = getQuizQuestions($quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?> - Preview</title>
    <link rel="stylesheet" href="../assets/css/view_quiz.css">
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main>
        <div class="quiz-wrapper">
            <a href="quiz.php" class="back-link">
                <i class='bx bx-arrow-back'></i> Back to Quizzes
            </a>

            <div class="quiz-progress-floating">
                <div class="progress-inner">
                    <div class="progress-text">
                        Question <span id="currentQuestion">1</span> of <?= count($questions) ?>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>
            </div>

            <div class="quiz-container">
                <div class="quiz-header">
                    <div class="preview-badge">
                        <i class='bx bx-show'></i> Preview Mode
                    </div>
                    <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                    <p class="description"><?= htmlspecialchars($quiz['description']) ?></p>
                    <div class="quiz-metadata">
                        <span><i class='bx bx-calendar'></i> Created: <?= date('M d, Y', strtotime($quiz['created_at'])) ?></span>
                        <span><i class='bx bx-user'></i> Created by: <?= htmlspecialchars($quiz['teacher_name']) ?></span>
                        <?php if ($_SESSION['role'] === 'teacher'): ?>
                            <span><i class='bx bx-code-alt'></i> Code: <?= htmlspecialchars($quiz['quiz_code']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="questions-section">
                    <?php foreach ($quiz['questions'] as $index => $question): ?>
                        <div class="question-item">
                            <div class="question-header">
                                <h3>Question <?= $index + 1 ?></h3>
                                <span class="question-type">
                                    <?php 
                                        switch($question['type']) {
                                            case 'multiple_choice':
                                                echo 'Multiple Choice';
                                                break;
                                            case 'multiple_answer':
                                                echo 'Multiple Answer';
                                                break;
                                            case 'true_false':
                                                echo 'True/False';
                                                break;
                                            case 'short_answer':
                                                echo 'Short Answer';
                                                break;
                                        }
                                    ?>
                                </span>
                            </div>
                            
                            <p class="question-text"><?= htmlspecialchars($question['text']) ?></p>
                            
                            <div class="answer-options">
                                <?php switch($question['type']): 
                                    case 'multiple_choice': ?>
                                        <?php foreach ($question['answers'] as $answer): ?>
                                            <div class="answer-option">
                                                <input type="radio" 
                                                       id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                                       name="q_<?= $question['question_id'] ?>" 
                                                       disabled>
                                                <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                                    <?= htmlspecialchars($answer['text']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php break; ?>

                                    <?php case 'multiple_answer': ?>
                                        <?php foreach ($question['answers'] as $answer): ?>
                                            <div class="answer-option">
                                                <input type="checkbox" 
                                                       id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                                       name="q_<?= $question['question_id'] ?>[]" 
                                                       disabled>
                                                <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                                    <?= htmlspecialchars($answer['text']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php break; ?>

                                    <?php case 'true_false': ?>
                                        <?php foreach ($question['answers'] as $answer): ?>
                                            <div class="answer-option">
                                                <input type="radio" 
                                                       id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                                       name="q_<?= $question['question_id'] ?>" 
                                                       disabled>
                                                <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                                    <?= htmlspecialchars($answer['text']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php break; ?>

                                    <?php case 'short_answer': ?>
                                        <div class="text-answer-preview">
                                            <p class="answer-placeholder">Students will type their answer here...</p>
                                        </div>
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="quiz-actions">
                    <?php if ($_SESSION['role'] === 'teacher'): ?>
                        <a href="preview_quiz.php?id=<?= $quizId ?>" class="preview-quiz-btn">
                            <i class='bx bx-play'></i> Preview Quiz
                        </a>
                        <button onclick="editQuiz(<?= $quizId ?>)" class="edit-quiz-btn">
                            <i class='bx bxs-edit'></i> Edit Quiz
                        </button>
                        <button onclick="confirmDelete(<?= $quizId ?>)" class="delete-quiz-btn">
                            <i class='bx bxs-trash'></i> Delete Quiz
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        const questionCards = document.querySelectorAll('.question-card');
        const currentQuestionSpan = document.getElementById('currentQuestion');
        const progressFill = document.getElementById('progressFill');
        const totalQuestions = questionCards.length;

        // Show first question by default
        if (questionCards.length > 0) {
            questionCards[0].classList.add('active');
            updateProgress(1);
        }

        function updateProgress(currentQuestion) {
            currentQuestionSpan.textContent = currentQuestion;
            const progress = (currentQuestion / totalQuestions) * 100;
            progressFill.style.width = `${progress}%`;
        }

        // Navigation between questions
        questionCards.forEach((card, index) => {
            card.addEventListener('click', () => {
                questionCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                updateProgress(index + 1);
            });
        });

        function confirmDelete(quizId) {
            if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
                fetch('../actions/delete_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ quiz_id: quizId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect back to quiz list
                        window.location.href = 'quiz.php';
                    } else {
                        alert(data.message || 'Error deleting quiz');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the quiz');
                });
            }
        }

        function editQuiz(quizId) {
            window.location.href = `edit_quiz.php?id=${quizId}`;
        }
    </script>
</body>
</html> 