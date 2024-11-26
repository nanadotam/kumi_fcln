<?php
session_start();
require_once '../functions/quiz_functions.php';
require_once '../functions/auth_functions.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

// Database connection
$db = new mysqli("localhost", "root", "", "kumidb");

// Validate quiz access
$stmt = $db->prepare("
    SELECT q.*, u.role 
    FROM Quizzes q 
    JOIN Users u ON u.user_id = ?
    WHERE q.quiz_id = ? AND (
        q.mode = 'individual' OR 
        (q.mode = 'group' AND EXISTS (
            SELECT 1 FROM GroupMembers gm 
            WHERE gm.user_id = ? AND gm.group_id IN (
                SELECT group_id FROM GroupMembers
            )
        ))
    )
");

$stmt->bind_param("iii", $userId, $quizId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: dashboard.php');
    exit();
}

$quiz = $result->fetch_assoc();

// Get questions and answers
$stmt = $db->prepare("
    SELECT q.*, a.answer_id, a.answer_text, a.is_correct 
    FROM Questions q
    LEFT JOIN Answers a ON q.question_id = a.question_id
    WHERE q.quiz_id = ?
    ORDER BY q.order_position, q.question_id
");

$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questionId = $row['question_id'];
    if (!isset($questions[$questionId])) {
        $questions[$questionId] = [
            'question_id' => $questionId,
            'text' => $row['question_text'],
            'type' => $row['type'],
            'points' => $row['points'],
            'answers' => []
        ];
    }
    if ($row['answer_id']) {
        $questions[$questionId]['answers'][] = [
            'answer_id' => $row['answer_id'],
            'text' => $row['answer_text'],
            'is_correct' => $row['is_correct']
        ];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $responses = $_POST['responses'] ?? [];
    $totalScore = 0;
    $totalPoints = 0;
    
    // Start transaction
    $db->begin_transaction();
    
    try {
        // Insert quiz result
        $stmt = $db->prepare("
            INSERT INTO QuizResults (quiz_id, user_id, score) 
            VALUES (?, ?, 0)
        ");
        $stmt->bind_param("ii", $quizId, $userId);
        $stmt->execute();
        $resultId = $db->insert_id;
        
        // Process each response
        foreach ($questions as $question) {
            $questionId = $question['question_id'];
            $response = $responses["q_{$questionId}"] ?? null;
            $isCorrect = 0;
            $selectedAnswerId = null;
            $textResponse = null;
            
            if ($question['type'] === 'short_answer') {
                $textResponse = $response;
                // You might want to implement a more sophisticated way to check text answers
                $isCorrect = !empty($textResponse) ? 1 : 0;
            } else {
                $selectedAnswerId = $response;
                // Check if the selected answer is correct
                foreach ($question['answers'] as $answer) {
                    if ($answer['answer_id'] == $selectedAnswerId) {
                        $isCorrect = $answer['is_correct'];
                        break;
                    }
                }
            }
            
            // Insert response
            $stmt = $db->prepare("
                INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct, text_response)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiiis", $resultId, $questionId, $selectedAnswerId, $isCorrect, $textResponse);
            $stmt->execute();
            
            if ($isCorrect) {
                $totalScore += $question['points'];
            }
            $totalPoints += $question['points'];
        }
        
        // Update final score
        $finalScore = ($totalScore / $totalPoints) * 100;
        $stmt = $db->prepare("UPDATE QuizResults SET score = ? WHERE result_id = ?");
        $stmt->bind_param("di", $finalScore, $resultId);
        $stmt->execute();
        
        $db->commit();
        
        // Redirect to results page
        header("Location: quiz_result.php?id={$resultId}");
        exit();
        
    } catch (Exception $e) {
        $db->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="quiz-progress-floating">
        <div class="progress-inner">
            <div class="progress-text">
                Question <span id="currentQuestion">1</span> of <?= count($quiz['questions']) ?>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
    </div>

    <div class="quiz-wrapper">
        <div class="quiz-container">
            <div class="quiz-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <p class="description"><?= htmlspecialchars($quiz['description']) ?></p>
            </div>
            
            <form method="POST" action="">
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="question-card" data-question="<?= $index + 1 ?>">
                        <div class="question-header">
                            <h3>Question <?= $index + 1 ?></h3>
                            <span class="question-type"><?= ucfirst($question['type']) ?></span>
                        </div>
                        <p class="question-text"><?= htmlspecialchars($question['text']) ?></p>
                        
                        <div class="answer-container">
                            <?php if ($question['type'] === 'multiple_choice'): ?>
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                               name="responses[q_<?= $question['question_id'] ?>]" 
                                               value="<?= $answer['answer_id'] ?>"
                                               required>
                                        <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                            <?= htmlspecialchars($answer['text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-answer">
                                    <textarea 
                                        name="responses[q_<?= $question['question_id'] ?>]"
                                        class="text-input"
                                        placeholder="Enter your answer here..."
                                        required
                                        rows="6"
                                    ></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="quiz-actions">
                    <button type="submit" class="submit-btn">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Progress tracking
        const questionCards = document.querySelectorAll('.question-card');
        const currentQuestionSpan = document.getElementById('currentQuestion');
        const progressFill = document.getElementById('progressFill');
        const totalQuestions = questionCards.length;

        function updateProgress() {
            let answeredQuestions = 0;
            
            // Check multiple choice answers
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            const radioGroups = {};
            
            radioInputs.forEach(input => {
                const name = input.getAttribute('name');
                if (!radioGroups[name]) {
                    radioGroups[name] = false;
                }
                if (input.checked) {
                    radioGroups[name] = true;
                }
            });
            
            // Count answered radio groups
            answeredQuestions += Object.values(radioGroups).filter(Boolean).length;
            
            // Check text answers
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                if (textarea.value.trim() !== '') {
                    answeredQuestions++;
                }
            });
            
            // Update progress bar and counter
            const progress = (answeredQuestions / totalQuestions) * 100;
            progressFill.style.width = `${progress}%`;
            currentQuestionSpan.textContent = Math.min(answeredQuestions + 1, totalQuestions);
        }

        // Add event listeners for both radio buttons and textareas
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', updateProgress);
        });

        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', updateProgress);
        });

        // Initial progress check
        updateProgress();
    </script>
</body>
</html> 