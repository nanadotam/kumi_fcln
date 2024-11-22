let questionCounter = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', addQuestion);
    }
    
    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
});

function addQuestion() {
    questionCounter++;
    const questionsContainer = document.getElementById('questionsContainer');
    
    const questionCard = createQuestionCard();
    questionsContainer.appendChild(questionCard);
}

function createQuestionCard() {
    const card = document.createElement('div');
    card.className = 'question-card';
    card.dataset.questionId = questionCounter;
    
    card.innerHTML = `
        <div class="question-header">
            <div class="question-main">
                <input type="text" class="question-text" placeholder="Enter your question">
                <div class="question-settings">
                    <div class="points-input">
                        <label>Points:</label>
                        <input type="number" class="question-points" value="1" min="1">
                    </div>
                    <select class="question-type" onchange="handleQuestionTypeChange(this)">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="paragraph">Paragraph</option>
                        <option value="checkbox">Select All That Apply</option>
                    </select>
                </div>
            </div>
            <button class="btn-delete" onclick="deleteQuestion(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <div class="options-container">
            <!-- Options will be added here -->
        </div>
    `;

    // Initialize with multiple choice options by default
    const optionsContainer = card.querySelector('.options-container');
    initializeMultipleChoice(optionsContainer);

    return card;
}

function handleQuestionTypeChange(select) {
    const optionsContainer = select.closest('.question-card').querySelector('.options-container');
    optionsContainer.innerHTML = ''; // Clear existing options

    switch(select.value) {
        case 'multiple_choice':
            initializeMultipleChoice(optionsContainer);
            break;
        case 'checkbox':
            initializeCheckbox(optionsContainer);
            break;
        case 'paragraph':
            initializeParagraph(optionsContainer);
            break;
    }
}

function initializeMultipleChoice(container) {
    container.innerHTML = `
        <div class="options-list">
            ${createOption('radio', 1)}
            ${createOption('radio', 2)}
        </div>
        <button class="btn-add-option" onclick="addOption(this, 'radio')">
            <i class='bx bx-plus'></i> Add Option
        </button>
    `;
}

function initializeCheckbox(container) {
    container.innerHTML = `
        <div class="options-list">
            ${createOption('checkbox', 1)}
            ${createOption('checkbox', 2)}
        </div>
        <button class="btn-add-option" onclick="addOption(this, 'checkbox')">
            <i class='bx bx-plus'></i> Add Option
        </button>
    `;
}

function initializeParagraph(container) {
    container.innerHTML = `
        <div class="paragraph-answer">
            <textarea disabled placeholder="Students will type their answer here" class="answer-preview"></textarea>
        </div>
    `;
}

function createOption(type, index) {
    return `
        <div class="option-item">
            <input type="${type}" disabled>
            <input type="text" class="option-input" placeholder="Option ${index}">
            <button class="btn-delete-option" onclick="deleteOption(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
}

function addOption(button, type) {
    const optionsList = button.previousElementSibling;
    const optionCount = optionsList.children.length + 1;
    const newOption = document.createElement('div');
    newOption.innerHTML = createOption(type, optionCount);
    optionsList.appendChild(newOption.firstElementChild);
}

function deleteOption(button) {
    const optionItem = button.closest('.option-item');
    const optionsList = optionItem.parentElement;
    optionItem.remove();
    
    // Renumber remaining options
    optionsList.querySelectorAll('.option-item').forEach((item, index) => {
        item.querySelector('.option-input').placeholder = `Option ${index + 1}`;
    });
}

function deleteQuestion(button) {
    const questionCard = button.closest('.question-card');
    questionCard.remove();
}

function saveQuiz() {
    const quiz = {
        title: document.getElementById('quizTitle').value,
        description: document.getElementById('quizDescription').value,
        dueDate: document.getElementById('quizDueDate').value,
        questions: []
    };

    document.querySelectorAll('.question-card').forEach(card => {
        const question = {
            text: card.querySelector('.question-text').value,
            type: card.querySelector('.question-type').value,
            points: parseInt(card.querySelector('.question-points').value),
            options: []
        };

        if (question.type !== 'paragraph') {
            card.querySelectorAll('.option-input').forEach(option => {
                question.options.push(option.value);
            });
        }

        quiz.questions.push(question);
    });

    // Send to server
    fetch('../actions/save_quiz.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(quiz)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quiz saved successfully!');
            window.location.href = 'quizzes.php';
        } else {
            alert('Error saving quiz: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving quiz. Please try again.');
    });
}