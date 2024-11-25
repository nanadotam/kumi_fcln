document.addEventListener('DOMContentLoaded', function() {
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    // Add event listeners
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', () => createQuestion());
    }
    
    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
    
    // Load existing questions if in edit mode
    if (typeof existingQuestions !== 'undefined' && existingQuestions.length > 0) {
        existingQuestions.forEach(question => {
            createQuestion(question);
        });
    }
});

function createQuestion(existingQuestion = null) {
    const questionsContainer = document.getElementById('questionsContainer');
    questionCount++;
    
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-card';
    if (existingQuestion) {
        questionDiv.dataset.questionId = existingQuestion.question_id;
    }
    
    let options = [];
    if (existingQuestion && existingQuestion.options) {
        try {
            options = existingQuestion.options.split(',').map(opt => JSON.parse(opt));
        } catch (e) {
            console.error('Error parsing options:', e);
        }
    }
    
    questionDiv.innerHTML = `
        <div class="question-header">
            <h3>Question ${questionCount}</h3>
            <button type="button" class="delete-question" onclick="deleteQuestion(this)">
                <i class='bx bx-trash'></i> Delete
            </button>
        </div>
        <div class="form-group">
            <label>Question Text</label>
            <textarea class="question-text" required>${existingQuestion ? existingQuestion.question_text : ''}</textarea>
        </div>
        <div class="form-group">
            <label>Question Type</label>
            <select class="question-type" onchange="handleQuestionTypeChange(this)">
                <option value="">Select question type...</option>
                <option value="multiple_choice" ${existingQuestion?.question_type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                <option value="checkbox" ${existingQuestion?.question_type === 'checkbox' ? 'selected' : ''}>Multiple Answer</option>
                <option value="paragraph" ${existingQuestion?.question_type === 'paragraph' ? 'selected' : ''}>Short Answer</option>
            </select>
        </div>
        <div class="form-group">
            <label>Points</label>
            <input type="number" class="question-points" min="1" value="${existingQuestion ? existingQuestion.points : 1}" required>
        </div>
        <div class="options-container">
            ${generateOptionsHTML(existingQuestion?.question_type, options)}
        </div>
    `;
    
    questionsContainer.appendChild(questionDiv);
    
    if (existingQuestion) {
        const select = questionDiv.querySelector('.question-type');
        handleQuestionTypeChange(select);
    }
}

function editQuiz(quizId) {
    if (!quizId) return;
    window.location.href = `edit_quiz.php?id=${quizId}`;
}

