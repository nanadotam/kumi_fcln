<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';
require_once '../functions/student_functions.php';


// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];
$availableQuizzes = getAvailableQuizzes($studentId);
$completedQuizzes = getCompletedQuizzes($studentId);
$progress = getStudentProgress($studentId);
$upcomingDeadlines = getUpcomingDeadlines($studentId);
$notifications = getUserNotifications($studentId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
        </div>
        <div class="nav-links">
            <div class="notification-dropdown">
                <button class="notification-btn">
                    <i class='bx bx-bell'></i>
                    <?php if (count(array_filter($notifications, fn($n) => !$n['read']))): ?>
                        <span class="notification-badge"></span>
                    <?php endif; ?>
                </button>
                <div class="notification-content">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?= $notification['read'] ? '' : 'unread' ?>"
                             data-id="<?= $notification['id'] ?>">
                            <p><?= $notification['message'] ?></p>
                            <small><?= $notification['created_at'] ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="profile.php">Profile</a>
            <a href="../actions/logout.php">Logout</a>
        </div>
    </nav>

    <main class="dashboard">
        <section class="welcome-section">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name']) ?></h1>
            <p class="welcome-message">Track your progress and take your quizzes</p>
            <div class="welcome-content">
                <div class="stats-overview">
                    <div class="stat-card">
                        <i class='bx bx-check-circle'></i>
                        <div class="stat-info">
                            <h3>Completed</h3>
                            <p><?= count($completedQuizzes) ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-book-open'></i>
                        <div class="stat-info">
                            <h3>Available</h3>
                            <p><?= count($availableQuizzes) ?></p>
                        </div>
                    </div>
                </div>
                <div class="welcome-image">
                    <img src="../assets/imgs/studentdash.svg" alt="Student Dashboard">
                </div>
            </div>
        </section>

        <section class="progress-section">
            <h2>Your Progress</h2>
            <div class="progress-cards">
                <div class="progress-card">
                    <h3>Completion Rate</h3>
                    <p><?= round(($progress['completed_quizzes'] / $progress['total_quizzes']) * 100) ?>%</p>
                </div>
                <div class="progress-card">
                    <h3>Average Score</h3>
                    <p><?= round($progress['average_score'], 1) ?>%</p>
                </div>
                <div class="progress-card">
                    <h3>Quizzes Passed</h3>
                    <p><?= $progress['quizzes_passed'] ?></p>
                </div>
            </div>
        </section>

        <section class="deadlines-section">
            <h2>Upcoming Deadlines</h2>
            <div class="deadline-list">
                <?php foreach ($upcomingDeadlines as $deadline): ?>
                    <div class="deadline-item">
                        <span class="deadline-title"><?= $deadline['title'] ?></span>
                        <span class="deadline-date"><?= $deadline['deadline'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="available-quizzes">
            <h2>Available Quizzes</h2>
            <div class="quiz-list">
                <?php foreach ($availableQuizzes as $quiz): ?>
                    <a href="take_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="quiz-item">
                        <span class="quiz-title"><?= $quiz['title'] ?></span>
                        <div class="quiz-info">
                            <span class="deadline">Deadline: <?= $quiz['deadline'] ?></span>
                            <span class="mode"><?= ucfirst($quiz['mode']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="completed-quizzes">
            <h2>Completed Quizzes</h2>
            <div class="quiz-list">
                <?php foreach ($completedQuizzes as $quiz): ?>
                    <a href="quiz_result.php?id=<?= $quiz['result_id'] ?>" class="quiz-item">
                        <span class="quiz-title"><?= $quiz['title'] ?></span>
                        <div class="quiz-info">
                            <span class="score">Score: <?= $quiz['score'] ?>%</span>
                            <span class="completed-date"><?= $quiz['submitted_at'] ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script src="../assets/js/notifications.js"></script>
</body>
</html> 