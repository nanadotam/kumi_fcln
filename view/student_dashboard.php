<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';
require_once '../functions/student_functions.php';

$currentPage = 'dashboard';
include_once '../components/sidebar.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];
$availableQuizzes = getAvailableQuizzes($studentId);
// $completedQuizzes = getCompletedQuizzes($studentId);
// $progress = getStudentProgress($studentId);
// $upcomingDeadlines = getUpcomingDeadlines($studentId);

// Get only recent completed quizzes (last 5)
$recentCompletedQuizzes = array_slice($completedQuizzes, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Student Dashboard - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-container">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Student') ?>!</h1>
            <p>Here's your learning progress:</p>
            
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3> Completion Rate </h3>
                    <p class="stat-number">
                        <?= isset($progress['total_quizzes']) ? round(($progress['completed_quizzes'] / max(1, $progress['total_quizzes'])) * 100) : 0 ?>%
                    </p>
                </div>
                <div class="stat-box">
                    <h3>Average Score</h3>
                    <p class="stat-number"><?= round($progress['average_score'] ?? 0, 1) ?>%</p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section-card">
                <h2>Recent Scores</h2>
                <div class="quiz-list">
                    <?php if (!empty($recentCompletedQuizzes)): ?>
                        <?php foreach ($recentCompletedQuizzes as $quiz): ?>
                            <div class="quiz-item">
                                <div>
                                    <h4><?= htmlspecialchars($quiz['title']) ?></h4>
                                    <small><?= date('M d, Y', strtotime($quiz['submitted_at'])) ?></small>
                                </div>
                                <span class="score-badge <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                    <?= $quiz['score'] ?>%
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent scores available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-card">
                <h2>Upcoming Quizzes</h2>
                <div class="quiz-list">
                    <?php if (!empty($upcomingDeadlines)): ?>
                        <?php foreach ($upcomingDeadlines as $deadline): ?>
                            <div class="quiz-item">
                                <div>
                                    <h4><?= htmlspecialchars($deadline['title']) ?></h4>
                                    <small>Due: <?= date('M d, Y', strtotime($deadline['deadline'])) ?></small>
                                </div>
                                <a href="take_quiz.php?id=<?= $deadline['quiz_id'] ?>" class="start-quiz-btn">Start Quiz</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming quizzes at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
