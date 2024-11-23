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

// Fetch all required data
// $availableQuizzes = getAvailableQuizzes($studentId);
$completedQuizzes = getCompletedQuizzes($studentId);
// $progress = getStudentProgress($studentId);
// $upcomingDeadlines = getUpcomingDeadlines($studentId);

// Get only recent completed quizzes (last 5)
  // $recentCompletedQuizzes = array_slice($completedQuizzes, 0, 5);

// Calculate additional stats for display
$totalQuizzes = $progress['total_quizzes'] ?? 0;
$completedQuizCount = $progress['completed_quizzes'] ?? 0;
$completionRate = $totalQuizzes > 0 ? round(($completedQuizCount / $totalQuizzes) * 100) : 0;
$averageScore = round($progress['average_score'] ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Kumi</title>
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <div class="dashboard-container">
        <section class="welcome-section">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Student') ?>!</h1>
            <div class="stats-overview">
                <div class="stat-card">
                    <i class='bx bx-book-open'></i>
                    <div class="stat-info">
                        <h3>Completion Rate</h3>
                        <p><?= $completionRate ?>%</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class='bx bx-task'></i>
                    <div class="stat-info">
                        <h3>Average Score</h3>
                        <p><?= $averageScore ?>%</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="quizcode-container">
            <h2>Ready for your next quiz?</h2>
            <p>Enter the quiz code to start</p>
            <input type="text" name="quizcode" id="quizcode" placeholder="Enter Quiz Code">
            <button class="start-btn">Start Quiz</button>
        </div>

        <div class="dashboard-sections">
            <div class="section-card">
                <h2>Recent Scores</h2>
                <div class="quiz-grid">
                    <?php if (!empty($recentCompletedQuizzes)): ?>
                        <?php foreach ($recentCompletedQuizzes as $quiz): ?>
                            <div class="quiz-card">
                                <div class="quiz-card-header">
                                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                                    <span class="score-badge <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                        <?= $quiz['score'] ?>%
                                    </span>
                                </div>
                                <div class="quiz-meta">
                                    <span><i class='bx bx-calendar'></i> <?= date('M d, Y', strtotime($quiz['submitted_at'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent scores available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
