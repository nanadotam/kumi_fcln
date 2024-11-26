<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

$currentPage = 'dashboard';
include_once '../components/sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$teacherId = $_SESSION['user_id'];
$quizzes = getQuizzesByTeacher($teacherId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/teacher_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <main class="dashboard">
        <section class="welcome-section">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name']) ?></h1>
            <p class="welcome-message">Create and manage your quizzes</p>
            <div class="welcome-content">
                <div class="stats-overview">
                    <div class="stat-card">
                        <i class='bx bx-book-content'></i>
                        <div class="stat-info">
                            <h3>Total Quizzes</h3>
                            <p><?= count($quizzes) ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-group'></i>
                        <div class="stat-info">
                            <h3>Total Attempts</h3>
                            <p><?= array_sum(array_column($quizzes, 'attempt_count')) ?></p>
                        </div>
                    </div>
                </div>
                <div class="create-quiz-button">
                    <a href="quiz.php">Create Quiz</a>
                </div>
            </div>
        </section>

        <section class="recent-quizzes">
            <h2>Recent Quizzes</h2>
            <div class="quiz-list">
                <?php foreach (array_slice($quizzes, 0, 5) as $quiz): ?>
                    <a href="view_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="quiz-item">
                        <div class="quiz-info">
                            <span class="quiz-title"><?= $quiz['title'] ?></span>
                            <span class="quiz-meta">
                                <i class='bx bx-time-five'></i>
                                <?= date('M d, Y', strtotime($quiz['created_at'])) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script src="../assets/js/teacher_dashboard.js"></script>
    <script src="../assets/js/quiz.js"></script>
</body>
</html>

