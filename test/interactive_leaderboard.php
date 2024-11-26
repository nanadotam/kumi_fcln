<?php
session_start();
require_once '../utils/Database.php';

// Initialize database connection
$db = Database::getInstance();

// Fetch current user's data
$userId = $_SESSION['user_id'] ?? null;
$userData = null;
if ($userId) {
    $query = "SELECT * FROM Users WHERE user_id = ?";
    $result = $db->query($query, [$userId]);
    $userData = $result->fetch_assoc();
}

// Fetch leaderboard data
$leaderboardQuery = "
    SELECT 
        u.user_id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        COUNT(qr.result_id) as questions_answered,
        SUM(qr.score) as total_score,
        RANK() OVER (ORDER BY SUM(qr.score) DESC) as rank
    FROM Users u
    LEFT JOIN QuizResults qr ON u.user_id = qr.user_id
    WHERE qr.submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY u.user_id
    ORDER BY total_score DESC
    LIMIT 10
";
$result = $db->query($leaderboardQuery);
$leaderboardData = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumi Learning Leaderboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Oswald:wght@400;500;600&family=Overpass+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="interactive_leaderboard_styles.css">
</head>
<body>
    <div class="l-wrapper">
        <header class="c-header">
            <div class="c-logo">
                <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo" class="c-logo__img">
                <span>Learning Progress</span>
            </div>
            <button class="c-button c-button--primary">Start Quiz</button>
        </header>
        
        <div class="l-grid">
            <div class="l-grid__item l-grid__item--sticky">
                <div class="c-card u-bg--light-gradient u-text--dark">
                    <div class="c-card__body">
                        <div class="u-display--flex u-justify--space-between">
                            <div class="u-text--left">
                                <div class="u-text--small">My Progress</div>
                                <h2><?= $userData ? "#{$userData['rank']}" : 'Not Ranked' ?></h2>
                            </div>
                            <div class="u-text--right">
                                <div class="u-text--small">Total Score</div>
                                <h2><?= $userData ? number_format($userData['total_score']) : '0' ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="c-card">
                    <div class="c-card__body">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-label">Quizzes Completed</div>
                                <div class="stat-value"><?= $userData ? $userData['quizzes_completed'] : '0' ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Success Rate</div>
                                <div class="stat-value"><?= $userData ? round($userData['success_rate'], 1) : '0' ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="l-grid__item">
                <div class="c-card">
                    <div class="c-card__header">
                        <h3>Top Learners</h3>
                        <select class="c-select">
                            <option value="week" selected>This Week</option>
                            <option value="month">This Month</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                    <div class="c-card__body">
                        <ul class="c-list" id="list">
                            <li class="c-list__item c-list__header">
                                <div class="c-list__grid">
                                    <div class="u-text--left u-text--small u-text--medium">Rank</div>
                                    <div class="u-text--left u-text--small u-text--medium">Student</div>
                                    <div class="u-text--right u-text--small u-text--medium">Score</div>
                                </div>
                            </li>
                            <?php foreach ($leaderboardData as $student): ?>
                            <li class="c-list__item <?= $student['id'] === $userId ? 'current-user' : '' ?>">
                                <div class="c-list__grid">
                                    <div class="rank"><?= $student['rank'] ?></div>
                                    <div class="c-media">
                                        <div class="c-media__content">
                                            <div class="c-media__title"><?= htmlspecialchars($student['name']) ?></div>
                                            <div class="u-text--small"><?= $student['questions_answered'] ?> questions</div>
                                        </div>
                                    </div>
                                    <div class="score"><?= number_format($student['total_score']) ?></div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="interactive_leaderboard_script.js"></script>
</body>
</html>

