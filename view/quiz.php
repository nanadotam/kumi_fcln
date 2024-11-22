<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';


$currentPage = 'quiz';
//include_once '../components/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$quizId = $_GET['id'] ?? null;

// Get appropriate quizzes based on user role
if ($userRole === 'student') {
    $availableQuizzes = getAvailableQuizzes($userId);
} else {
    $quizzes = getQuizzesByTeacher($userId);
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
    <main class="quiz-page">
        <?php if ($userRole === 'teacher'): ?>
            <!-- Teacher View -->
            <div class="page-header">
                <h1>My Quizzes</h1>
                <a href="create_quiz.php" class="create-btn">
                    <i class='bx bx-plus'></i>
                    Create Quiz
                </a>
            </div>

            <div class="filters">
                <input type="text" id="search-quiz" placeholder="Search quizzes...">
                <select id="filter-mode">
                    <option value="">All Modes</option>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                </select>
            </div>

            <div class="quiz-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-card" data-mode="<?= $quiz['mode'] ?>">
                        <div class="quiz-card-header">
                            <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                            <span class="mode-badge <?= $quiz['mode'] ?>">
                                <?= ucfirst($quiz['mode']) ?>
                            </span>
                        </div>
                        <p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
                        <div class="quiz-meta">
                            <span><i class='bx bx-user'></i> <?= $quiz['attempt_count'] ?> attempts</span>
                            <span><i class='bx bx-calendar'></i> <?= date('M d, Y', strtotime($quiz['created_at'])) ?></span>
                        </div>
                        <div class="quiz-actions">
                            <a href="quiz_results.php?id=<?= $quiz['quiz_id'] ?>" class="view-btn">View Results</a>
                            <button onclick="editQuiz(<?= $quiz['quiz_id'] ?>)" class="edit-btn">
                                <i class='bx bxs-edit'></i>
                            </button>
                            <button onclick="deleteQuiz(<?= $quiz['quiz_id'] ?>)" class="delete-btn">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Student View -->
            <div class="page-header">
                <h1>Available Quizzes</h1>
            </div>

            <div class="quiz-sections">
                <section class="available-quizzes">
                    <h2>Ready to Take</h2>
                    <div class="quiz-grid">
                        <?php foreach ($availableQuizzes as $quiz): ?>
                            <div class="quiz-card">
                                <div class="quiz-card-header">
                                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                                    <span class="mode-badge <?= $quiz['mode'] ?>">
                                        <?= ucfirst($quiz['mode']) ?>
                                    </span>
                                </div>
                                <p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
                                <?php if ($quiz['deadline']): ?>
                                    <div class="deadline-info">
                                        <i class='bx bx-time-five'></i>
                                        <span>Due: <?= date('M d, Y', strtotime($quiz['deadline'])) ?></span>
                                    </div>
                                <?php endif; ?>
                                <a href="take_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="start-btn">
                                    Start Quiz
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="completed-quizzes">
                    <h2>Completed Quizzes</h2>
                    <div class="quiz-grid">
                        <?php foreach ($completedQuizzes as $quiz): ?>
                            <div class="quiz-card completed">
                                <div class="quiz-card-header">
                                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                                    <span class="score-badge">
                                        <?= $quiz['score'] ?>%
                                    </span>
                                </div>
                                <div class="quiz-meta">
                                    <span>
                                        <i class='bx bx-calendar'></i>
                                        Completed: <?= date('M d, Y', strtotime($quiz['submitted_at'])) ?>
                                    </span>
                                </div>
                                <a href="quiz_result.php?id=<?= $quiz['result_id'] ?>" class="view-result-btn">
                                    View Results
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>

    <script src="../assets/js/quiz.js"></script>
</body>
</html>

