<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$teacherId = $_SESSION['user_id'];
$quizzes = getQuizzesByTeacher($teacherId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content remains the same -->
</head>
<body>
    <!-- Previous HTML content -->
    <div class="quiz-list">
        <?php foreach ($quizzes as $quiz): ?>
            <a href="quiz_results.php?id=<?= $quiz['quiz_id'] ?>" class="quiz-item-link">
                <div class="quiz-item">
                    <span class="quiz-title"><?= $quiz['title'] ?></span>
                    <div class="quiz-actions">
                        <button class="edit" onclick="editQuiz(<?= $quiz['quiz_id'] ?>)">
                            <i class='bx bxs-edit'></i>
                        </button>
                        <button class="delete" onclick="deleteQuiz(<?= $quiz['quiz_id'] ?>)">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</body>
</html>
