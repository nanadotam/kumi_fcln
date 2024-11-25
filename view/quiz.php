<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';


$currentPage = 'quiz';
include_once '../components/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$quizId = $_GET['id'] ?? null;

// Get appropriate quizzes based on user role
if ($userRole === 'student') {
    $quizzes = getAvailableQuizzes($userId);
} else {
    $quizzes = getQuizzesByTeacher($userId);
}

$completedQuizIds = [];
if ($userRole === 'student') {
    $completedQuizzes = getCompletedQuizzes($userId);
    $completedQuizIds = array_column($completedQuizzes, 'quiz_id');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main class="quiz-page">
        <div class="quiz-sections">
            <div class="page-header">
                <h1>My Quizzes</h1>
                <?php if ($userRole === 'teacher'): ?>
                    <a href="create_quiz.php" class="create-btn">
                        <i class='bx bx-plus'></i> Create Quiz
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="quiz-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-card" data-quiz-id="<?= $quiz['quiz_id'] ?>">
                        <div class="quiz-card-header">
                            <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                            <?php if (isset($quiz['score'])): ?>
                                <span class="score-badge <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                    <?= $quiz['score'] ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="quiz-meta">
                            <span><i class='bx bx-calendar'></i> <?= date('M d, Y', strtotime($quiz['created_at'])) ?></span>
                            <?php if ($_SESSION['role'] === 'teacher'): ?>
                                <span><i class='bx bx-code-alt'></i> Code: <?= htmlspecialchars($quiz['quiz_code']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="quiz-actions">
                            <?php if ($userRole === 1): ?>
                                <?php if (in_array($quiz['quiz_id'], $completedQuizIds)): ?>
                                    <a href="quiz_result.php?id=<?= $quiz['quiz_id'] ?>" class="view-btn">View Results</a>
                                <?php else: ?>
                                    <a href="take_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="take-btn">Take Quiz</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="quiz_results.php?id=<?= $quiz['quiz_id'] ?>" class="view-btn">View Results</a>
                                <button class="edit-btn" onclick="editQuiz(<?= $quiz['quiz_id'] ?>)">
                                    <i class='bx bxs-edit'></i> Edit
                                </button>
                                <button class="delete-btn" onclick="deleteQuiz(<?= $quiz['quiz_id'] ?>)" title="Delete Quiz">
                                    <i class='bx bx-trash'></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script src="../assets/js/quiz.js"></script>
</body>
</html>

