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
    <title>Live Leaderboard - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/leaderboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>

    <main class="leaderboard-page">
        <div class="leaderboard-container">
            <div class="leaderboard-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <div class="quiz-info">
                    <span><i class='bx bx-code'></i> Quiz Code: <?= $quizCode ?></span>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats-container">
                <div class="stat-box">
                    <i class='bx bx-user'></i>
                    <span id="participantCount">0</span>
                    <label>Participants</label>
                </div>
                <div class="stat-box">
                    <i class='bx bx-trophy'></i>
                    <span id="averageScore">0</span>
                    <label>Average Score</label>
                </div>
            </div>

            <!-- Leaderboard Table -->
            <div class="leaderboard-table">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardBody">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="../assets/js/leaderboard.js"></script>
    <script>
        // Initialize leaderboard
        const leaderboard = new Leaderboard('<?= $quizCode ?>');
    </script>
</body>
</html> 