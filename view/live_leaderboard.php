<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Get quiz code from URL
$quizCode = $_GET['code'] ?? null;

if (!$quizCode) {
    header('Location: dashboard.php');
    exit();
}

// Get quiz details
$quiz = getQuizByCode($quizCode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Leaderboard - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/leaderboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="leaderboard-wrapper">
        <div class="leaderboard-header">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo" class="logo">
            <div class="quiz-info">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <div class="quiz-code">
                    <span>Quiz Code: <?= htmlspecialchars($quizCode) ?></span>
                </div>
            </div>
        </div>

        <div class="leaderboard-grid">
            <div class="leaderboard-stats">
                <div class="stats-card">
                    <div class="my-rank">
                        <small>My Rank</small>
                        <h2><?= $myRank ?></h2>
                    </div>
                    <div class="my-score">
                        <small>My Score</small>
                        <h2><?= $myScore ?></h2>
                    </div>
                </div>
            </div>

            <div class="leaderboard-main">
                <div class="podium">
                    <div class="podium-spot second">
                        <div class="avatar">🥈</div>
                        <span class="name">-</span>
                        <span class="score">0</span>
                    </div>
                    <div class="podium-spot first">
                        <div class="crown">👑</div>
                        <div class="avatar">🥇</div>
                        <span class="name">-</span>
                        <span class="score">0</span>
                    </div>
                    <div class="podium-spot third">
                        <div class="avatar">🥉</div>
                        <span class="name">-</span>
                        <span class="score">0</span>
                    </div>
                </div>

                <div class="leaderboard-list" id="leaderboardList">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/leaderboard.js"></script>
</body>
</html> 