<?php
session_start();
require_once '../functions/quiz_functions.php';
require_once '../functions/auth_functions.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

// Validate quiz access
if (!$quizId || !canAccessQuiz($userId, $quizId)) {
    header('Location: dashboard.php');
    exit();
}

$quiz = getQuizById($quizId);
if (!$quiz) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $responses = $_POST['responses'] ?? [];
    $quizResults = [];
    $totalScore = 0;
    $totalQuestions = count($quiz['questions']);
    $correctAnswers = 0;

    foreach ($quiz['questions'] as $question) {
        $questionId = $question['question_id'];
        $response = $responses["q_{$questionId}"] ?? null;
        
        if ($question['type'] === 'multiple_choice') {
            $isCorrect = validateMultipleChoice($questionId, $response);
            if ($isCorrect) $correctAnswers++;
        } else {
            // Text answer validation
            $points = validateTextAnswer($questionId, $response);
            $correctAnswers += ($points > 0) ? 1 : 0;
        }
        
        // Store response
        $quizResults[] = [
            'question_id' => $questionId,
            'response' => $response,
            'is_correct' => $isCorrect ?? false,
            'points' => $points ?? 0
        ];
    }

    // Calculate final score
    $score = ($correctAnswers / $totalQuestions) * 100;
    
    // Save results to database
    $resultId = saveQuizResults($userId, $quizId, $score, $quizResults);
    
    // Redirect to results page
    header("Location: quiz_result.php?id={$resultId}");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
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
        <div class="quiz-container">
            <div class="quiz-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <p class="description"><?= htmlspecialchars($quiz['description']) ?></p>
            </div>
            
            <form method="POST" action="">
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
                                               value="<?= $answer['answer_id'] ?>"
                                               required>
                                        <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                            <?= htmlspecialchars($answer['text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-answer">
                                    <textarea 
                                        name="responses[q_<?= $question['question_id'] ?>]"
                                        class="text-input"
                                        placeholder="Enter your answer here..."
                                        required
                                        rows="6"
                                    ></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="quiz-actions">
                    <button type="submit" class="submit-btn">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Simple progress tracking
        const questionCards = document.querySelectorAll('.question-card');
        const currentQuestionSpan = document.getElementById('currentQuestion');
        const progressFill = document.getElementById('progressFill');
        const totalQuestions = questionCards.length;

        questionCards.forEach(card => {
            const inputs = card.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    const questionNumber = card.dataset.question;
                    currentQuestionSpan.textContent = questionNumber;
                    progressFill.style.width = (questionNumber / totalQuestions * 100) + '%';
                });
            });
        });
    </script>
</body>
</html> 