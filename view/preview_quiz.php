<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Authentication and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
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
    $_SESSION['error'] = "Quiz not found";
    header('Location: quiz.php');
    exit();
}

// Verify teacher owns this quiz
if (!verifyQuizOwnership($quizId, $_SESSION['user_id'])) {
    header('Location: quiz.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>

    <div class="preview-mode-banner">
        <i class='bx bx-show'></i> Preview Mode - This is how students will see your quiz
    </div>

    <div class="quiz-progress-floating">
        <div class="progress-inner">
            <div class="progress-text">
                Question <span id="currentQuestion">1</span> of <?= count($quiz['questions']) ?>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
    </div>

    <div class="quiz-wrapper">
        <a href="view_quiz.php?id=<?= $quizId ?>" class="back-link">
            <i class='bx bx-arrow-back'></i> Back to Quiz Overview
        </a>

        <div class="quiz-container">
            <div class="quiz-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <p class="description"><?= htmlspecialchars($quiz['description']) ?></p>
            </div>
            
            <form>
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="question-card" data-question="<?= $index + 1 ?>">
                        <div class="question-header">
                            <h3>Question <?= $index + 1 ?></h3>
                            <span class="question-type"><?= ucfirst($question['type']) ?></span>
                        </div>
                        <p class="question-text"><?= htmlspecialchars($question['text']) ?></p>
                        
                        <div class="answer-container">
                            <?php if ($question['type'] === 'multiple_choice'): ?>
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                               name="responses[q_<?= $question['question_id'] ?>]" 
                                               value="<?= $answer['answer_id'] ?>">
                                        <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                            <?= htmlspecialchars($answer['text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-answer">
                                    <textarea 
                                        class="text-input"
                                        placeholder="Students will type their answer here..."
                                        rows="6"
                                    ></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="quiz-actions">
                    <button type="button" class="submit-btn" disabled>Submit Quiz</button>
                    <p class="preview-note">This is a preview - submissions are disabled</p>
                </div>
            </form>
        </div>
    </div>
    
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
    </script>
</body>
</html> 