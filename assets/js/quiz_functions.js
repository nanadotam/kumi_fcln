function createMultipleChoiceOptions(container, questionId) {
    container.innerHTML = `
        <div class="multiple-choice-options">
            <div class="options-list"></div>
            <button type="button" class="add-option-btn" onclick="addOption(this, 'radio', ${questionId})">
                <i class='bx bx-plus-circle'></i> Add Option
            </button>
        </div>
    `;
    // Add initial options
    addOption(container.querySelector('.add-option-btn'), 'radio', questionId);
    addOption(container.querySelector('.add-option-btn'), 'radio', questionId);
}

function createMultipleAnswerOptions(container, questionId) {
    container.innerHTML = `
        <div class="multiple-answer-options">
            <div class="options-list"></div>
            <button type="button" class="add-option-btn" onclick="addOption(this, 'checkbox', ${questionId})">
                <i class='bx bx-plus-circle'></i> Add Option
            </button>
        </div>
    `;
    // Add initial options
    addOption(container.querySelector('.add-option-btn'), 'checkbox', questionId);
    addOption(container.querySelector('.add-option-btn'), 'checkbox', questionId);
}

function createShortAnswerField(container, questionId) {
    container.innerHTML = `
        <div class="short-answer-field">
            <div class="preview-area">
                <textarea disabled placeholder="Students will type their answer here" class="student-answer-preview"></textarea>
            </div>
            <div class="model-answer">
                <label>Model Answer (for grading reference):</label>
                <textarea name="questions[${questionId}][model_answer]" class="model-answer-input" 
                          placeholder="Enter the expected answer here..." required></textarea>
            </div>
        </div>
    `;
} 