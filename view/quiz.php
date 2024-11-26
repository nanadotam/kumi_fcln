<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';
require_once '../db/config.php';

$currentPage = 'quiz';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Get quizzes based on user role
if ($userRole === 'student') {
    // Get only completed quizzes for students
    $stmt = $conn->prepare("
        SELECT 
            q.quiz_id,
            q.title,
            q.quiz_code,
            qr.result_id,
            qr.score,
            qr.submitted_at as completion_date,
            COUNT(DISTINCT qst.question_id) as total_questions,
            SUM(CASE WHEN r.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers
        FROM QuizResults qr
        JOIN Quizzes q ON qr.quiz_id = q.quiz_id
        LEFT JOIN Questions qst ON q.quiz_id = qst.quiz_id
        LEFT JOIN Responses r ON qr.result_id = r.result_id AND qst.question_id = r.question_id
        WHERE qr.user_id = ?
        GROUP BY q.quiz_id, qr.result_id
        ORDER BY qr.submitted_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quizzes = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Keep existing teacher quiz retrieval
    $quizzes = getQuizzesByTeacher($userId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes - Kumi</title>
    <link rel="stylesheet" href="../assets/css/quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main class="quiz-page">
        <div class="quiz-sections">
            <?php if ($userRole === 'student'): ?>
                <div class="page-header">
                    <h1>My Quiz History</h1>
                    <div class="quiz-code-section">
                        <form id="quizCodeForm" onsubmit="return false;">
                            <input type="text" 
                                   name="quiz_code" 
                                   id="quiz_code"
                                   placeholder="Enter Quiz Code" 
                                   required>
                            <button type="button" class="start-btn" onclick="verifyQuizCode()">
                                <i class='bx bx-play'></i> Start Quiz
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="page-header">
                    <h1>My Quizzes</h1>
                    <a href="create_quiz_form.php" class="create-btn">
                        <i class='bx bx-plus'></i> Create Quiz
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="quiz-grid">
                <?php if ($userRole === 'student' && empty($quizzes)): ?>
                    <div class="no-quizzes">
                        <i class='bx bx-book-open'></i>
                        <p>You haven't taken any quizzes yet.</p>
                        <p>Enter a quiz code above to start a new quiz!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="quiz-card" data-quiz-id="<?= $quiz['quiz_id'] ?>">
                            <div class="quiz-card-header">
                                <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                                <?php if ($userRole === 'student'): ?>
                                    <span class="score-badge <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                        <?= $quiz['score'] ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="quiz-meta">
                                <?php if ($userRole === 'student'): ?>
                                    <span>
                                        <i class='bx bx-calendar'></i> 
                                        Completed: <?= date('M d, Y', strtotime($quiz['completion_date'])) ?>
                                    </span>
                                    <span>
                                        <i class='bx bx-check-circle'></i>
                                        <?= $quiz['correct_answers'] ?>/<?= $quiz['total_questions'] ?> Correct
                                    </span>
                                <?php else: ?>
                                    <span>
                                        <i class='bx bx-calendar'></i> 
                                        Created: <?= date('M d, Y', strtotime($quiz['created_at'])) ?>
                                    </span>
                                    <span>
                                        <i class='bx bx-code-alt'></i> 
                                        Code: <?= htmlspecialchars($quiz['quiz_code']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="quiz-actions">
                                <?php if ($userRole === 'student'): ?>
                                    <a href="quiz_result.php?id=<?= $quiz['result_id'] ?>" class="view-btn">
                                        <i class='bx bx-show'></i> View Results
                                    </a>
                                <?php else: ?>
                                    <a href="view_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="view-btn">
                                        <i class='bx bx-show'></i> View Quiz
                                    </a>
                                    <button class="delete-btn" onclick="deleteQuiz(<?= $quiz['quiz_id'] ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../assets/js/take_quiz.js"></script>
    <script>
    function verifyQuizCode() {
        const quizCode = document.getElementById('quiz_code').value.trim();
        
        if (!quizCode) {
            alert('Please enter a quiz code');
            return;
        }

        // Send the code to verify_quiz_code.php
        fetch('../actions/verify_quiz_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ quiz_code: quizCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `take_quiz.php?id=${data.quiz_id}`;
            } else {
                alert(data.message || 'Invalid quiz code. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    </script>
</body>
</html>

