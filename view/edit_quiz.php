function createQuestion() {
    questionCount++; // Increment question count
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-card';

    questionDiv.innerHTML = `
        <div class="question-header">
            <h3>Question ${questionCount}</h3>
            <button type="button" class="delete-question" onclick="deleteQuestion(this)">
                <i class='bx bx-trash'></i> Delete
            </button>
        </div>
        <div class="form-group">
            <label>Question Text</label>
            <textarea class="question-text" placeholder="Enter your question here..." required></textarea>
        </div>
        <div class="form-group">
            <label>Question Type</label>
            <select class="question-type" onchange="handleQuestionTypeChange(this)">
                <option value="">Select question type...</option>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="checkbox">Multiple Answer</option>
                <option value="paragraph">Short Answer</option>
            </select>
        </div>
        <div class="form-group">
            <label>Points</label>
            <input type="number" class="question-points" min="1" value="1" required>
        </div>
        <div class="options-container">
            <!-- Options will be added dynamically based on question type -->
        </div>
    `;

    // Append the new question card to the container
    questionsContainer.appendChild(questionDiv);
}
