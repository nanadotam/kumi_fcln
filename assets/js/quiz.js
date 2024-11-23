document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const questionsContainer = document.getElementById('questionsContainer');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    let questionCount = 0;  // Add question counter

    addQuestionBtn.addEventListener('click', function() {
        console.log('Add Question Button Clicked');
        questionCount++;    // Increment counter
        addNewQuestion();
    });
    
    // Add event delegation for the entire questions container
    questionsContainer.addEventListener('click', function(e) {
        // Handle delete option button clicks
        if (e.target.classList.contains('btn-delete')) {
            const optionContainer = e.target.closest('.option-container');
            if (optionContainer) {
                deleteOption(e.target);
                // Renumber remaining options after deletion
                const questionCard = optionContainer.closest('.question-card');
                renumberOptions(questionCard);
            }
        }
        
        // Handle add option button clicks
        if (e.target.classList.contains('btn-add-option') || 
            e.target.closest('.btn-add-option')) {
            const questionCard = e.target.closest('.question-card');
            addOption(questionCard.querySelector('.btn-add-option'));
        }
    });
    
    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
});

function addNewQuestion() {
    const questionHTML = `
        <div class="question-card" data-question-id="${Date.now()}">
            <div class="question-header">
                <div class="question-main">
                    <input type="text" class="question-text" placeholder="Question ${questionCount}">
                    <div class="question-settings">
                        <select class="question-type" onchange="handleQuestionTypeChange(this)">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="checkbox">Multiple Select</option>
                            <option value="text">Short Answer</option>
                        </select>
                        <input type="number" class="points-input" placeholder="Points" min="1" value="1">
                    </div>
                </div>
                <button class="btn-delete" onclick="deleteQuestion(this)" title="Delete Question">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
            
            <div class="options-container">
                <div class="option-container">
                    <div class="correct-answer-toggle" onclick="toggleCorrectAnswer(this)" title="Mark as correct answer"></div>
                    <input type="text" class="option-input" placeholder="Option 1">
                    <span class="correct-answer-label">Correct Answer</span>
                    <button class="btn-delete" onclick="deleteOption(this)" title="Delete Option">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
                <button class="btn-add-option" onclick="addOption(this)">
                    <i class='bx bx-plus'></i> Add Option
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', questionHTML);
}

function handleQuestionTypeChange(select) {
    const optionsContainer = select.closest('.question-card').querySelector('.options-container');
    const type = select.value;
    
    switch(type) {
        case 'multiple_choice':
            createMultipleChoiceOptions(optionsContainer);
            break;
        case 'checkbox':
            createCheckboxOptions(optionsContainer);
            break;
        case 'paragraph':
            createParagraphOption(optionsContainer);
            break;
    }
}

function createMultipleChoiceOptions(container) {
    container.innerHTML = `
        <div class="option-item">
            <input type="radio" disabled>
            <input type="text" class="option-input" placeholder="Option 1">
            <button class="btn-delete" onclick="deleteOption(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <div class="option-item">
            <input type="radio" disabled>
            <input type="text" class="option-input" placeholder="Option 2">
            <button class="btn-delete" onclick="deleteOption(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <button class="btn-add" onclick="addOption(this.parentElement, 'radio')">
            <i class='bx bx-plus'></i> Add Option
        </button>
    `;
}

function createCheckboxOptions(container) {
    container.innerHTML = `
        <div class="option-item">
            <input type="checkbox" disabled>
            <input type="text" class="option-input" placeholder="Option 1">
            <button class="btn-delete" onclick="deleteOption(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <div class="option-item">
            <input type="checkbox" disabled>
            <input type="text" class="option-input" placeholder="Option 2">
            <button class="btn-delete" onclick="deleteOption(this)">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <button class="btn-add" onclick="addOption(this.parentElement, 'checkbox')">
            <i class='bx bx-plus'></i> Add Option
        </button>
    `;
}

function createParagraphOption(container) {
    container.innerHTML = `
        <div class="option-item">
            <textarea disabled placeholder="Students will type their answer here" 
                      style="width: 100%; min-height: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
        </div>
    `;
}

function addOption(container, type) {
    const optionCount = container.querySelectorAll('.option-item').length + 1;
    const newOption = document.createElement('div');
    newOption.className = 'option-item';
    newOption.innerHTML = `
        <input type="${type}" disabled>
        <input type="text" class="option-input" placeholder="Option ${optionCount}">
        <button class="btn-delete" onclick="deleteOption(this)">
            <i class='bx bx-trash'></i>
        </button>
    `;
    
    // Insert before the "Add Option" button
    container.insertBefore(newOption, container.lastElementChild);
}

function deleteOption(button) {
    const optionItem = button.closest('.option-item');
    const container = optionItem.parentElement;
    optionItem.remove();
    
    // Renumber remaining options
    container.querySelectorAll('.option-item').forEach((item, index) => {
        const input = item.querySelector('.option-input');
        if (input) {
            input.placeholder = `Option ${index + 1}`;
        }
    });
}

function deleteQuestion(button) {
    button.closest('.question-card').remove();
}

function saveQuiz() {
    // Generate a random 6-character alphanumeric code
    const generateQuizCode = () => {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 6; i++) {
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return code;
    };

    const quiz = {
        title: document.getElementById('quizTitle')?.value || 'Untitled Quiz',
        description: document.getElementById('quizDescription')?.value || '',
        dueDate: document.getElementById('quizDueDate')?.value || '',
        quiz_code: generateQuizCode(),
        questions: []
    };

    if (!quiz.title.trim()) {
        alert('Quiz title is required.');
        return;
    }

    document.querySelectorAll('.question-card').forEach(card => {
        const questionText = card.querySelector('.question-text')?.value.trim();
        const questionType = card.querySelector('.question-type')?.value;

        if (!questionText) {
            alert('All questions must have text.');
            return;
        }

        const question = {
            text: questionText,
            type: questionType,
            options: []
        };

        if (question.type !== 'paragraph') {
            card.querySelectorAll('.option-input').forEach(option => {
                const optionValue = option.value.trim();
                if (optionValue) {
                    question.options.push(optionValue);
                }
            });
        }

        quiz.questions.push(question);
    });

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
                alert(`Quiz saved successfully! Quiz Code: ${quiz.quiz_code}`);
                window.location.href = '../view/quiz.php';
                
            } else {
                alert('Error saving quiz: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving quiz. Please try again.');
        });
}

function editQuiz(quizId) {
    window.location.href = `edit_quiz.php?id=${quizId}`;
}

function deleteQuiz(quizId) {
    if (confirm('Are you sure you want to delete this quiz?')) {
        fetch('../actions/delete_quiz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ quiz_id: quizId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const quizCard = document.querySelector(`[data-quiz-id="${quizId}"]`);
                quizCard.remove();
                showNotification('Quiz deleted successfully', 'success');
            } else {
                showNotification('Error deleting quiz', 'error');
            }
        });
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
