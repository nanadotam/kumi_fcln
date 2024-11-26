<?php
function renderQuizCodeModal($error = null) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Leaderboard - Enter Code</title>
        <link rel="stylesheet" href="../assets/css/interactive_leaderboard.css">
        <link rel="stylesheet" href="../assets/css/leaderboard_modal.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <?php include_once 'sidebar.php'; ?>
        
        <main class="main-content">
            <div id="quizCodeModal" class="modal active">
                <div class="modal-content">
                    <h2><i class='bx bx-trophy'></i> View Quiz Leaderboard</h2>
                    
                    <?php if ($error): ?>
                        <div class="error-message">
                            <i class='bx bx-error-circle'></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="interactive_leaderboard.php" method="GET">
                        <div class="form-group">
                            <label for="code">Enter Quiz Code</label>
                            <input type="text" 
                                   id="code" 
                                   name="code" 
                                   placeholder="Enter 6-digit quiz code"
                                   pattern="[A-Za-z0-9]{6}"
                                   maxlength="6"
                                   required>
                            <small>Enter the 6-character code provided by your teacher</small>
                        </div>
                        <div class="modal-actions">
                            <a href="quiz.php" class="btn btn-secondary">
                                <i class='bx bx-arrow-back'></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-trophy'></i> View Leaderboard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <script src="../assets/js/leaderboard.js"></script>
    </body>
    </html>
    <?php
}
?> 