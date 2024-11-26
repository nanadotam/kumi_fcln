<?php
session_start();
require_once '../utils/Database.php';
require_once '../components/quiz_code_modal.php';

// Add authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['role'];
$currentPage = 'leaderboard';

// Initialize database connection
$db = Database::getInstance();

// Get quiz code from URL or modal input
$quizCode = $_GET['code'] ?? $_POST['quiz_code'] ?? null;

if (!$quizCode) {
    // Show quiz code input modal
    renderQuizCodeModal();
    exit();
}

// Get quiz details
$quizQuery = "SELECT * FROM Quizzes WHERE quiz_code = ?";
$quizResult = $db->query($quizQuery, [$quizCode]);
$quiz = $quizResult->fetch_assoc();

if (!$quiz) {
    // Show error message instead of redirecting
    $error = "Quiz not found. Please check the quiz code and try again.";
    renderQuizCodeModal();
    exit();
}

// Fetch current user's data for this quiz
$userId = $_SESSION['user_id'] ?? null;
$userData = null;
if ($userId) {
    $userQuery = "
        SELECT 
            u.*,
            qr.score as quiz_score,
            RANK() OVER (ORDER BY qr.score DESC) as rank
        FROM Users u
        LEFT JOIN QuizResults qr ON u.user_id = qr.user_id
        WHERE u.user_id = ? AND qr.quiz_id = ?
    ";
    $result = $db->query($userQuery, [$userId, $quiz['quiz_id']]);
    $userData = $result->fetch_assoc();
}

// Fetch quiz-specific leaderboard data
$leaderboardQuery = "
    SELECT 
        u.user_id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        qr.score as total_score,
        COUNT(r.response_id) as questions_answered,
        RANK() OVER (ORDER BY qr.score DESC) as rank
    FROM Users u
    JOIN QuizResults qr ON u.user_id = qr.user_id
    LEFT JOIN Responses r ON qr.result_id = r.result_id
    WHERE qr.quiz_id = ?
    GROUP BY u.user_id, qr.score
    ORDER BY qr.score DESC
    LIMIT 10
";
$result = $db->query($leaderboardQuery, [$quiz['quiz_id']]);
$leaderboardData = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?> - Leaderboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Oswald:wght@400;500;600&family=Overpass+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/interactive_leaderboard_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="sidebar sidebar-leaderboard">
        <div class="logo-container">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
        </div>
        <nav class="nav-links">
            <a href="<?= $userRole ?>_dashboard.php">
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
            <a href="interactive_leaderboard.php" class="active">
                <i class='bx bx-trophy'></i>
                <span>Leaderboard</span>
            </a>
            <a href="#" onclick="confirmLogout(event)">
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
    
    <div class="l-wrapper leaderboard-page">
        <header class="c-header">
            <div class="c-logo">
                <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo" class="c-logo__img">
                <span><?= htmlspecialchars($quiz['title']) ?> Leaderboard</span>
            </div>
            <a href="quiz.php" class="c-button c-button--primary">Back to Quizzes</a>
        </header>
        
        <div class="l-grid">
            <div class="l-grid__item l-grid__item--sticky">
                <div class="c-card u-bg--light-gradient u-text--dark">
                    <div class="c-card__body">
                        <div class="u-display--flex u-justify--space-between">
                            <div class="u-text--left">
                                <div class="u-text--small">My Rank</div>
                                <h2><?= $userData ? "#{$userData['rank']}" : 'Not Attempted' ?></h2>
                            </div>
                            <div class="u-text--right">
                                <div class="u-text--small">My Score</div>
                                <h2><?= $userData ? number_format($userData['quiz_score']) : '0' ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="l-grid__item">
                <div class="c-card">
                    <div class="c-card__header">
                        <h3>Quiz Rankings</h3>
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
                            <li class="c-list__item <?= $student['user_id'] === $userId ? 'current-user' : '' ?>">
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

    <!-- Add modal for quiz code input -->
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <script src="../assets/js/interactive_leaderboard.js"></script>
    <script>
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../actions/logout.php';
        }
    }
    </script>
</body>
</html>

