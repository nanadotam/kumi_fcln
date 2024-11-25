I'll help brainstorm the live quiz feature. Based on the existing codebase and requirements, here's what we should consider:

1. **Quiz Mode Separation**
From the code in `quiz.php` (lines 20-24), we already have a mode system in place. We should add 'live' as a new quiz mode alongside 'individual' and 'group'.

2. **New Pages Needed:**
- `live_quiz_teacher.php`: Teacher's control interface
- `live_quiz_student.php`: Student's quiz-taking interface
- `live_leaderboard.php`: Real-time leaderboard (shared view)

3. **Database Changes:**
We'll need to add:
- `quiz_code` field for live session access
- `status` field for quiz state (waiting/active/completed)
- `current_question` field for tracking progress

4. **WebSocket Integration**
Looking at the `LiveQuizController` class (
```1:23:assets/js/live-quiz.js
class LiveQuizController {
    constructor(quizCode) {
        this.quizCode = quizCode;
        this.participants = new Map();
        this.currentQuestion = 0;
        this.status = 'waiting';
    }

    startQuiz() {
        this.status = 'active';
        this.broadcastQuestion();
    }

    simulateParticipant(studentName) {
        const studentId = Date.now();
        this.participants.set(studentId, {
            name: studentName,
            score: 0,
            answers: []
        });
        return studentId;
    }
}
```
), we can expand this to handle:
- Real-time score updates
- Question synchronization
- Student responses
- Timer management

5. **UI Components**
The existing CSS shows we have good styling foundations:

```90:160:assets/css/live-quiz.css
.simulation-status, .live-leaderboard {
    margin-top: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.simulation-status h3, .live-leaderboard h3 {
    color: var(--color-dark);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.student-list, .leaderboard-list {
    margin-top: 1rem;
}

.student-item {
    display: flex;
    justify-content: space-between;
    padding: 1rem;
    background: var(--color-bg);
    margin: 0.5rem 0;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.leaderboard-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--color-bg);
    margin: 0.5rem 0;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease;
}

.top-3 {
    transform: scale(1.02);
    background: linear-gradient(45deg, var(--color-primary), var(--color-secondary));
    color: white;
    border: none;
}

.student-name, .name {
    font-weight: 500;
    color: var(--color-dark);
}

.student-score, .score {
    font-weight: 600;
    color: var(--color-primary);
}

.rank {
    font-weight: bold;
    min-width: 2rem;
    color: var(--color-primary);
}

.top-3 .student-score,
.top-3 .score,
.top-3 .rank,
.top-3 .student-name,
.top-3 .name {
    color: white;
} 
```

We can adapt these for the live quiz features.

Here's a proposed structure for the live quiz student view:

```php
<?php
// live_quiz_student.php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Verify quiz code and access
$quizCode = $_GET['code'] ?? null;
if (!$quizCode || !verifyQuizAccess($quizCode, $_SESSION['user_id'])) {
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
    <title>Live Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/live-quiz.css">
</head>
<body>
    <div class="live-quiz-container">
        <!-- Question Display -->
        <div class="question-area">
            <div class="question-header">
                <h2 id="questionText"></h2>
                <div class="timer-display">
                    <span id="timeRemaining">20</span>s
                </div>
            </div>
            <div id="answerOptions" class="answer-options">
                <!-- Options populated by JavaScript -->
            </div>
        </div>

        <!-- Leaderboard Sidebar -->
        <div class="leaderboard-sidebar">
            <h3>Top Players</h3>
            <div id="liveLeaderboard" class="leaderboard-list">
                <!-- Leaderboard updated via WebSocket -->
            </div>
        </div>
    </div>

    <script src="../assets/js/live-quiz.js"></script>
</body>
</html>
```

Would you like me to continue with the teacher's view or the WebSocket implementation next?


---

I'll help you build out the live quiz system, starting with the teacher's interface and WebSocket implementation. Let's break this down into parts:

1. First, let's create the teacher's control interface (`live_quiz_teacher.php`):

```php:view/live_quiz_teacher.php
<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Ensure only teachers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
if (!$quizId) {
    header('Location: teacher_dashboard.php');
    exit();
}

$quiz = getQuizById($quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Quiz Control - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/live_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="live-quiz-container teacher-view">
        <div class="control-panel">
            <div class="quiz-info">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <div class="quiz-code">
                    Quiz Code: <span id="quizCode"><?= $quiz['quiz_code'] ?></span>
                    <button onclick="copyQuizCode()" class="copy-btn">
                        <i class='bx bx-copy'></i>
                    </button>
                </div>
                <div class="session-stats">
                    <div class="stat">
                        <i class='bx bx-user'></i>
                        <span id="participantCount">0</span> Participants
                    </div>
                    <div class="stat">
                        <i class='bx bx-time'></i>
                        <span id="timeElapsed">00:00</span>
                    </div>
                </div>
            </div>

            <div class="quiz-controls">
                <button id="startQuizBtn" class="btn-primary">
                    <i class='bx bx-play'></i> Start Quiz
                </button>
                <button id="nextQuestionBtn" class="btn-secondary" disabled>
                    <i class='bx bx-skip-next'></i> Next Question
                </button>
                <button id="endQuizBtn" class="btn-danger" disabled>
                    <i class='bx bx-stop'></i> End Quiz
                </button>
            </div>
        </div>

        <div class="content-area">
            <div class="current-question">
                <div class="question-header">
                    <h2>Current Question</h2>
                    <div class="timer" id="questionTimer">
                        <i class='bx bx-time'></i>
                        <span>20</span>s
                    </div>
                </div>
                <div id="questionDisplay">
                    <!-- Question content will be displayed here -->
                </div>
            </div>

            <div class="live-stats">
                <div class="response-chart">
                    <canvas id="responseChart"></canvas>
                </div>
                <div class="quick-stats">
                    <div class="stat-card">
                        <h3>Response Rate</h3>
                        <p id="responseRate">0%</p>
                    </div>
                    <div class="stat-card">
                        <h3>Correct Answers</h3>
                        <p id="correctRate">0%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="leaderboard-sidebar">
            <h2>Live Leaderboard</h2>
            <div id="leaderboard" class="leaderboard-list">
                <!-- Leaderboard entries will be updated dynamically -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/live-quiz-teacher.js"></script>
</body>
</html>
```

2. The corresponding CSS for the teacher's interface:

```css:assets/css/live_quiz.css
:root {
    --primary: #187045;
    --secondary: #2a9d8f;
    --accent: #ffc936;
    --danger: #dc3545;
    --success: #28a745;
    --background: #f5f7fa;
    --text: #2d3748;
    --border: #e2e8f0;
}

.live-quiz-container {
    display: grid;
    grid-template-columns: 300px 1fr 300px;
    gap: 2rem;
    padding: 2rem;
    height: 100vh;
    background: var(--background);
}

.control-panel {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.quiz-info {
    margin-bottom: 2rem;
}

.quiz-code {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--background);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.copy-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--primary);
    font-size: 1.2rem;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.copy-btn:hover {
    background: rgba(24, 112, 69, 0.1);
}

.session-stats {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text);
}

.quiz-controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.quiz-controls button {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-secondary {
    background: var(--secondary);
    color: white;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.content-area {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.current-question {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.timer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary);
}

.live-stats {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
}

.stat-card {
    background: var(--background);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.stat-card h3 {
    font-size: 0.9rem;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.stat-card p {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary);
}
```

3. The WebSocket implementation for the teacher's interface:

```javascript:assets/js/live-quiz-teacher.js
class LiveQuizTeacher {
    constructor(quizId, quizCode) {
        this.quizId = quizId;
        this.quizCode = quizCode;
        this.ws = null;
        this.currentQuestion = 0;
        this.participants = new Map();
        this.questionTimer = null;
        this.sessionTimer = null;
        this.questionDuration = 20; // seconds per question
        
        this.initializeWebSocket();
        this.initializeEventListeners();
        this.initializeCharts();
    }

    initializeWebSocket() {
        this.ws = new WebSocket(`ws://localhost:8080/quiz/${this.quizCode}`);
        
        this.ws.onopen = () => {
            console.log('Connected to quiz server');
            this.ws.send(JSON.stringify({
                type: 'teacher_join',
                quizId: this.quizId
            }));
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleWebSocketMessage(data);
        };

        this.ws.onclose = () => {
            console.log('Disconnected from quiz server');
            // Attempt to reconnect after 5 seconds
            setTimeout(() => this.initializeWebSocket(), 5000);
        };
    }

    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'student_join':
                this.handleStudentJoin(data.student);
                break;
            case 'student_answer':
                this.handleStudentAnswer(data.studentId, data.answer);
                break;
            case 'quiz_state':
                this.updateQuizState(data.state);
                break;
            case 'error':
                this.handleError(data.message);
                break;
        }
    }

    initializeEventListeners() {
        document.getElementById('startQuizBtn').addEventListener('click', () => this.startQuiz());
        document.getElementById('nextQuestionBtn').addEventListener('click', () => this.nextQuestion());
        document.getElementById('endQuizBtn').addEventListener('click', () => this.endQuiz());
    }

    startQuiz() {
        this.ws.send(JSON.stringify({
            type: 'start_quiz'
        }));
        
        this.updateControls('started');
        this.startSessionTimer();
        this.broadcastQuestion();
    }

    nextQuestion() {
        this.currentQuestion++;
        this.broadcastQuestion();
        this.resetQuestionTimer();
    }

    broadcastQuestion() {
        const question = this.getQuestionData(this.currentQuestion);
        if (question) {
            this.ws.send(JSON.stringify({
                type: 'new_question',
                question: question
            }));
            this.displayQuestion(question);
            this.startQuestionTimer();
        }
    }

    updateLeaderboard(standings) {
        const leaderboard = document.getElementById('leaderboard');
        leaderboard.innerHTML = standings
            .map((entry, index) => `
                <div class="leaderboard-entry ${index < 3 ? 'top-3' : ''}">
                    <span class="rank">${index + 1}</span>
                    <span class="name">${entry.name}</span>
                    <span class="score">${entry.score}</span>
                </div>
            `)
            .join('');
    }

    updateResponseChart(responses) {
        // Update Chart.js visualization
        this.responseChart.data.datasets[0].data = responses;
        this.responseChart.update();
    }

    startQuestionTimer() {
        let timeLeft = this.questionDuration;
        const timerDisplay = document.querySelector('#questionTimer span');
        
        this.questionTimer = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(this.questionTimer);
                this.handleQuestionTimeout();
            }
        }, 1000);
    }

    handleQuestionTimeout() {
        this.ws.send(JSON.stringify({
            type: 'question_ended'
        }));
        
        // Show correct answer and statistics
        this.showQuestionResults();
    }

    endQuiz() {
        this.ws.send(JSON.stringify({
            type: 'end_quiz'
        }));
        
        clearInterval(this.sessionTimer);
        clearInterval(this.questionTimer);
        this.showFinalResults();
    }
}

// Initialize the quiz controller when the page loads
document.addEventListener('DOMContentLoaded', () => {
    const quizId = new URLSearchParams(window.location.search).get('id');
    const quizCode = document.getElementById('quizCode').textContent;
    const quizController = new LiveQuizTeacher(quizId, quizCode);
});
```

Would you like me to continue with the student interface and shared leaderboard components next?



---

I'll continue with the student interface and shared leaderboard components. Let's build them out:

1. First, let's create the student's quiz interface (`live_quiz_student.php`):

```php:view/live_quiz_student.php
<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Verify student access and quiz code
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$quizCode = $_GET['code'] ?? null;
if (!$quizCode || !verifyQuizAccess($quizCode, $_SESSION['user_id'])) {
    header('Location: student_dashboard.php');
    exit();
}

$quiz = getQuizByCode($quizCode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/live_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="live-quiz-container student-view">
        <div class="quiz-header">
            <h1><?= htmlspecialchars($quiz['title']) ?></h1>
            <div class="student-info">
                <span class="student-name"><?= htmlspecialchars($_SESSION['first_name']) ?></span>
                <span class="score">Score: <span id="currentScore">0</span></span>
            </div>
        </div>

        <div class="question-container">
            <div class="waiting-screen" id="waitingScreen">
                <h2>Waiting for teacher to start the quiz...</h2>
                <div class="loader"></div>
            </div>

            <div class="question-area" id="questionArea" style="display: none;">
                <div class="timer-display">
                    <i class='bx bx-time'></i>
                    <span id="timeRemaining">20</span>s
                </div>
                
                <div class="question-content">
                    <h2 id="questionText"></h2>
                    <div id="answerOptions" class="answer-options">
                        <!-- Options will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <div class="feedback-area" id="feedbackArea" style="display: none;">
                <!-- Feedback will be shown here after answering -->
            </div>
        </div>

        <div class="leaderboard-sidebar">
            <h3>Top 10 Players</h3>
            <div id="leaderboard" class="leaderboard-list">
                <!-- Leaderboard entries will be updated dynamically -->
            </div>
        </div>
    </div>

    <script src="../assets/js/live-quiz-student.js"></script>
</body>
</html>
```

2. The student's WebSocket controller (`live-quiz-student.js`):

```javascript:assets/js/live-quiz-student.js
class LiveQuizStudent {
    constructor(quizCode, studentName) {
        this.quizCode = quizCode;
        this.studentName = studentName;
        this.ws = null;
        this.currentScore = 0;
        this.answerStreak = 0;
        this.lastAnswerTime = 0;
        
        this.initializeWebSocket();
        this.initializeEventListeners();
    }

    initializeWebSocket() {
        this.ws = new WebSocket(`ws://localhost:8080/quiz/${this.quizCode}`);
        
        this.ws.onopen = () => {
            this.ws.send(JSON.stringify({
                type: 'student_join',
                name: this.studentName
            }));
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleWebSocketMessage(data);
        };

        this.ws.onclose = () => {
            this.showError('Connection lost. Attempting to reconnect...');
            setTimeout(() => this.initializeWebSocket(), 5000);
        };
    }

    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'quiz_started':
                this.hideWaitingScreen();
                break;
            
            case 'new_question':
                this.displayQuestion(data.question);
                break;
            
            case 'question_ended':
                this.showQuestionFeedback(data.correctAnswer, data.statistics);
                break;
            
            case 'leaderboard_update':
                this.updateLeaderboard(data.standings);
                break;
            
            case 'quiz_ended':
                this.showFinalResults(data.results);
                break;
        }
    }

    displayQuestion(question) {
        const questionArea = document.getElementById('questionArea');
        const questionText = document.getElementById('questionText');
        const answerOptions = document.getElementById('answerOptions');
        
        questionText.textContent = question.text;
        answerOptions.innerHTML = '';
        
        question.options.forEach((option, index) => {
            const button = document.createElement('button');
            button.className = 'answer-option';
            button.textContent = option;
            button.onclick = () => this.submitAnswer(index);
            answerOptions.appendChild(button);
        });

        questionArea.style.display = 'block';
        this.startAnswerTimer();
    }

    submitAnswer(optionIndex) {
        const answerTime = Date.now() - this.lastAnswerTime;
        
        this.ws.send(JSON.stringify({
            type: 'student_answer',
            answer: optionIndex,
            time: answerTime
        }));

        // Disable all answer buttons after selection
        const buttons = document.querySelectorAll('.answer-option');
        buttons.forEach(button => button.disabled = true);
    }

    updateLeaderboard(standings) {
        const leaderboard = document.getElementById('leaderboard');
        leaderboard.innerHTML = standings
            .slice(0, 10) // Show only top 10
            .map((entry, index) => `
                <div class="leaderboard-entry ${entry.name === this.studentName ? 'current-user' : ''} ${index < 3 ? 'top-3' : ''}">
                    <span class="rank">${index + 1}</span>
                    <span class="name">${entry.name}</span>
                    <span class="score">${entry.score}</span>
                </div>
            `)
            .join('');
    }

    showQuestionFeedback(correctAnswer, statistics) {
        const feedbackArea = document.getElementById('feedbackArea');
        // Implementation of feedback display
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    const quizCode = new URLSearchParams(window.location.search).get('code');
    const studentName = document.querySelector('.student-name').textContent;
    const quizController = new LiveQuizStudent(quizCode, studentName);
});
```

Would you like me to continue with the shared leaderboard component and additional styling next?

----
I'll continue with the shared leaderboard component and additional styling. Let's create a reusable leaderboard component that can be used across both teacher and student views.

1. First, let's create a shared leaderboard component (`components/live_leaderboard.php`):

```php:components/live_leaderboard.php
<?php
function renderLeaderboard($isTeacherView = false) {
    $maxVisiblePlayers = $isTeacherView ? 15 : 10;
?>
    <div class="leaderboard-container">
        <div class="leaderboard-header">
            <h3>Live Leaderboard</h3>
            <div class="leaderboard-stats">
                <span class="participant-count">
                    <i class='bx bx-user'></i>
                    <span id="participantCount">0</span> Players
                </span>
                <?php if ($isTeacherView): ?>
                    <span class="response-rate">
                        <i class='bx bx-check-circle'></i>
                        <span id="responseRate">0</span>% Responded
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="podium">
            <div class="podium-spot second" id="secondPlace">
                <div class="avatar">🥈</div>
                <span class="name">-</span>
                <span class="score">0</span>
            </div>
            <div class="podium-spot first" id="firstPlace">
                <div class="crown">👑</div>
                <div class="avatar">🥇</div>
                <span class="name">-</span>
                <span class="score">0</span>
            </div>
            <div class="podium-spot third" id="thirdPlace">
                <div class="avatar">🥉</div>
                <span class="name">-</span>
                <span class="score">0</span>
            </div>
        </div>

        <div class="leaderboard-list" id="leaderboardList">
            <!-- Entries will be populated by JavaScript -->
        </div>
    </div>
<?php
}
?>
```

2. Additional CSS for the enhanced leaderboard (`assets/css/leaderboard.css`):

```css:assets/css/leaderboard.css
.leaderboard-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    width: 100%;
    max-width: 400px;
}

.leaderboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.leaderboard-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.podium {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: var(--background);
    border-radius: 12px;
}

.podium-spot {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.podium-spot:hover {
    transform: translateY(-5px);
}

.podium-spot.first {
    position: relative;
    padding-top: 2rem;
}

.crown {
    position: absolute;
    top: -10px;
    font-size: 1.5rem;
    animation: float 2s ease-in-out infinite;
}

.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.first .avatar { font-size: 2rem; }

.name {
    font-size: 0.9rem;
    font-weight: 500;
    margin: 0.25rem 0;
}

.score {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary);
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.leaderboard-list {
    max-height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.leaderboard-entry {
    display: grid;
    grid-template-columns: 0.5fr 2fr 1fr;
    padding: 0.75rem;
    margin: 0.5rem 0;
    border-radius: 8px;
    background: var(--background);
    transition: transform 0.2s ease;
}

.leaderboard-entry.current-user {
    border: 2px solid var(--primary);
    background: var(--primary-light);
}

.score-change {
    animation: scoreUpdate 1s ease;
}

@keyframes scoreUpdate {
    0% { background: var(--accent-light); }
    100% { background: var(--background); }
}
```

3. JavaScript for handling leaderboard updates (`assets/js/leaderboard.js`):

```javascript:assets/js/leaderboard.js
class LeaderboardManager {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            maxVisible: options.maxVisible || 10,
            animateChanges: options.animateChanges !== false,
            highlightCurrentUser: options.highlightCurrentUser !== false,
            currentUserId: options.currentUserId || null
        };
        
        this.currentStandings = new Map();
        this.initializeLeaderboard();
    }

    updateStandings(standings) {
        // Update podium first
        this.updatePodium(standings.slice(0, 3));
        
        // Update full leaderboard
        this.updateLeaderboardList(standings);
        
        // Update stats
        this.updateStats(standings.length);
    }

    updatePodium(topThree) {
        ['first', 'second', 'third'].forEach((position, index) => {
            const player = topThree[index] || { name: '-', score: 0 };
            const element = document.getElementById(`${position}Place`);
            if (element) {
                element.querySelector('.name').textContent = player.name;
                element.querySelector('.score').textContent = player.score;
            }
        });
    }

    updateLeaderboardList(standings) {
        const list = this.container.querySelector('.leaderboard-list');
        const currentEntries = new Map();
        
        standings.forEach((player, index) => {
            if (index < this.options.maxVisible) {
                const entry = this.createLeaderboardEntry(player, index + 1);
                currentEntries.set(player.id, entry);
            }
        });

        // Animate changes if enabled
        if (this.options.animateChanges) {
            this.animateChanges(currentEntries);
        } else {
            list.innerHTML = '';
            currentEntries.forEach(entry => list.appendChild(entry));
        }
    }

    createLeaderboardEntry(player, rank) {
        const entry = document.createElement('div');
        entry.className = `leaderboard-entry ${
            player.id === this.options.currentUserId ? 'current-user' : ''
        }`;
        
        entry.innerHTML = `
            <span class="rank">${rank}</span>
            <span class="name">${player.name}</span>
            <span class="score">${player.score}</span>
        `;
        
        return entry;
    }
}
```

This shared leaderboard component can be included in both the teacher and student views. Would you like me to show how to integrate it into the existing views next?

----