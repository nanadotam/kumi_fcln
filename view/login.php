<?php
include '../db/config.php';
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
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
        </div>
        <nav class="nav-links">
            <a href="student_dashboard.php" class="active">
                <i class='bx bxs-dashboard'></i>
                <span>Dashboard</span>
            </a>
            <a href="quiz.php">
                <i class='bx bx-book-content'></i>
                <span>Quizzes</span>
            </a>
            <a href="profile.php">
                <i class='bx bx-user'></i>
                <span>Profile</span>
            </a>
            <a href="../actions/logout.php">
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <main class="dashboard">
        <section class="welcome-section">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name']) ?></h1>
            <p class="welcome-message">Track your progress and take your quizzes</p>
            <div class="stats-overview">
                <div class="stat-card">
                    <i class='bx bx-check-circle'></i>
                    <div class="stat-info">
                        <h3>Completion Rate</h3>
                        <p><?= round(($progress['completed_quizzes'] / max(1, $progress['total_quizzes'])) * 100) ?>%</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class='bx bx-bar-chart'></i>
                    <div class="stat-info">
                        <h3>Average Score</h3>
                        <p><?= round($progress['average_score'], 1) ?>%</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid">
            <section class="recent-scores">
                <h2><i class='bx bx-trophy'></i> Recent Scores</h2>
                <div class="scores-list">
                    <?php foreach ($recentCompletedQuizzes as $quiz): ?>
                        <div class="score-card">
                            <div class="score-info">
                                <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                                <span class="date"><?= date('M d, Y', strtotime($quiz['submitted_at'])) ?></span>
                            </div>
                            <div class="score-value <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                <?= $quiz['score'] ?>%
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="upcoming-quizzes">
                <h2><i class='bx bx-calendar-event'></i> Upcoming Quizzes</h2>
                <div class="upcoming-list">
                    <?php foreach ($upcomingDeadlines as $deadline): ?>
                        <div class="upcoming-card">
                            <div class="quiz-info">
                                <h3><?= htmlspecialchars($deadline['title']) ?></h3>
                                <div class="deadline">
                                    <i class='bx bx-time-five'></i>
                                    <span>Due: <?= date('M d, Y', strtotime($deadline['deadline'])) ?></span>
                                </div>
                            </div>
                            <a href="take_quiz.php?id=<?= $deadline['quiz_id'] ?>" class="start-btn">Start Quiz</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>