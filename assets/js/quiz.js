document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const questionsContainer = document.getElementById('questionsContainer');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    let questionCount = 0;  // Add question counter

    function createQuestion() {
        questionCount++;
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
                <label>Points</label>
                <input type="number" class="question-points" min="1" value="1" required>
            </div>
            <div class="options-container">
                <div class="option">
                    <input type="radio" name="correct_${questionCount}" value="0" checked>
                    <input type="text" class="option-input" placeholder="Enter option 1..." required>
                    <label class="correct-label">
                        <input type="checkbox" class="is-correct" /> Correct Answer
                    </label>
                    <button type="button" class="delete-option" onclick="deleteOption(this)">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
                <div class="option">
                    <input type="radio" name="correct_${questionCount}" value="1">
                    <input type="text" class="option-input" placeholder="Enter option 2..." required>
                    <label class="correct-label">
                        <input type="checkbox" class="is-correct" /> Correct Answer
                    </label>
                    <button type="button" class="delete-option" onclick="deleteOption(this)">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
            </div>
            <button type="button" class="add-option" onclick="addOption(this)">
                <i class='bx bx-plus'></i> Add Option
            </button>
        `;
        questionsContainer.appendChild(questionDiv);
    }

    // Add option to a question
    window.addOption = function(button) {
        const optionsContainer = button.previousElementSibling;
        const optionCount = optionsContainer.children.length + 1;
        const questionNumber = button.parentElement.querySelector('.question-header h3').textContent.split(' ')[1];
        
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option';
        optionDiv.innerHTML = `
            <input type="radio" name="correct_${questionNumber}" value="${optionCount - 1}">
            <input type="text" class="option-input" placeholder="Option ${optionCount}" required>
            <label class="correct-label">
                <input type="checkbox" class="is-correct" /> Correct Answer
            </label>
            <button type="button" class="delete-option" onclick="deleteOption(this)">
                <i class='bx bx-x'></i>
            </button>
        `;
        optionsContainer.appendChild(optionDiv);
    };

    // Delete option
    window.deleteOption = function(button) {
        const option = button.parentElement;
        const optionsContainer = option.parentElement;
        if (optionsContainer.children.length > 2) {
            option.remove();
        } else {
            alert('A question must have at least 2 options');
        }
    };

    // Delete question
    window.deleteQuestion = function(button) {
        const question = button.closest('.question-card');
        question.remove();
        updateQuestionNumbers();
    };

    // Update question numbers after deletion
    function updateQuestionNumbers() {
        const questions = document.querySelectorAll('.question-card');
        questions.forEach((question, index) => {
            question.querySelector('.question-header h3').textContent = `Question ${index + 1}`;
            const options = question.querySelectorAll('.option input[type="radio"]');
            options.forEach(option => {
                option.name = `correct_${index + 1}`;
            });
        });
        questionCount = questions.length;
    }

    // Add click event listener to the Add Question button
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', createQuestion);
    }
    
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
            <div class="model-answer-container">
                <label>Model Answer (for grading reference):</label>
                <textarea class="model-answer" placeholder="Enter the correct answer here..."
                          style="width: 100%; min-height: 80px;"></textarea>
            </div>
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
            card.querySelectorAll('.option').forEach(optionDiv => {
                const optionInput = optionDiv.querySelector('.option-input');
                const isCorrect = optionDiv.querySelector('.is-correct').checked;
                
                const optionValue = optionInput.value.trim();
                if (optionValue) {
                    question.options.push({
                        text: optionValue,
                        is_correct: isCorrect
                    });
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
