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
                    <i class='bx bx-trash'></i>
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
                    <option value="true_false">True/False</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="multiple_answer">Multiple Answer</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>
            <div class="options-container">
                <!-- Options will be added here based on question type -->
            </div>
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
            <input type="radio" name="correct_${questionNumber}" value="${optionCount}">
            <input type="text" class="option-input" placeholder="Option ${optionCount + 1}" required>
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
        const questionCard = option.closest('.question-card');
        const questionType = questionCard.querySelector('.question-type').value;
        
        if ((questionType === 'multiple_choice' || questionType === 'checkbox') && 
            optionsContainer.children.length <= 2) {
            alert('Multiple choice questions must have at least 2 options');
            return;
        }
        
        option.remove();
        // Update option numbers if needed
        updateOptionNumbers(optionsContainer);
    };

    function updateOptionNumbers(container) {
        container.querySelectorAll('.option-item').forEach((item, index) => {
            const input = item.querySelector('.option-input');
            if (input) {
                input.placeholder = `Option ${index + 1}`;
            }
        });
    }

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
    optionsContainer.innerHTML = '';

    switch(select.value) {
        case 'true_false':
            createTrueFalseOptions(optionsContainer);
            break;
        case 'multiple_choice':
            createMultipleChoiceOptions(optionsContainer);
            break;
        case 'multiple_answer':
            createMultipleAnswerOptions(optionsContainer);
            break;
        case 'short_answer':
            createShortAnswerField(optionsContainer);
            break;
    }
}


function createTrueFalseOptions(container) {
    container.innerHTML = `
        <div class="true-false-options">
            <label class="option-card">
                <input type="radio" name="correct_${Date.now()}" value="true" required>
                <span class="option-text">True</span>
                <span class="checkmark"></span>
            </label>
            <label class="option-card">
                <input type="radio" name="correct_${Date.now()}" value="false" required>
                <span class="option-text">False</span>
                <span class="checkmark"></span>
            </label>
        </div>
    `;
}






function createMultipleChoiceOptions(container) {
    container.innerHTML = `
        <div class="multiple-choice-options">
            <div class="options-list"></div>
            <button type="button" class="add-option-btn" onclick="addMultipleChoiceOption(this)">
                <i class='bx bx-plus-circle'></i> Add Option
            </button>
        </div>
    `;
    // Add first two options by default
   // addMultipleChoiceOption(container.querySelector('.add-option-btn'));
    //ddMultipleChoiceOption(container.querySelector('.add-option-btn'));
}



function addMultipleChoiceOption(container) {
    const optionsList = container.querySelector('.options-list');
    const optionId = Date.now();
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-item';
    optionDiv.innerHTML = `
        <div class="option-input-group">
            <input type="radio" 
                   name="correct_${container.closest('.question-card').dataset.questionId}" 
                   value="${optionId}">
            <input type="text" 
                   class="option-text" 
                   placeholder="Enter option text..."
                   required>
            <button type="button" class="delete-option" onclick="this.closest('.option-item').remove()">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
    optionsList.appendChild(optionDiv);
}





function createMultipleAnswerOptions(container) {
    container.innerHTML = `
        <div class="multiple-answer-options">
            <div class="options-list"></div>
            <button type="button" class="add-option-btn" onclick="addMultipleAnswerOption(this)">
                <i class='bx bx-plus-circle'></i> Add Option
            </button>
        </div>
    `;
    // Add first two options by default
    addMultipleAnswerOption(container.querySelector('.add-option-btn'));
    addMultipleAnswerOption(container.querySelector('.add-option-btn'));
}



function addMultipleAnswerOption(container) {
    const optionsList = container.querySelector('.options-list');
    const optionId = Date.now();
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-item';
    optionDiv.innerHTML = `
        <div class="option-input-group">
            <input type="checkbox" 
                   name="correct_${container.closest('.question-card').dataset.questionId}[]" 
                   value="${optionId}">
            <input type="text" 
                   class="option-text" 
                   placeholder="Enter option text..."
                   required>
            <button type="button" class="delete-option" onclick="this.closest('.option-item').remove()">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
    optionsList.appendChild(optionDiv);
}





function createShortAnswerField(container) {
    container.innerHTML = `
        <div class="short-answer-field">
            <div class="preview-area">
                <textarea disabled placeholder="Students will type their answer here" class="student-answer-preview"></textarea>
            </div>
            <div class="model-answer">
                <label>Model Answer (for grading reference):</label>
                <textarea class="model-answer-input" placeholder="Enter the expected answer here..."></textarea>
            </div>
        </div>
    `;
}



function addOption(button, type) {
    const container = button.parentElement;
    // Get all existing options
    const existingOptions = container.querySelectorAll('.option');
    // New option will be number of existing options + 1
    const newOptionNumber = existingOptions.length + 1;
    const questionNumber = button.closest('.question-card').querySelector('.question-header h3').textContent.split(' ')[1];
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option';
    optionDiv.innerHTML = `
        <input type="${type}" name="correct_${questionNumber}" value="${newOptionNumber}">
        <input type="text" class="option-input" placeholder="Option ${newOptionNumber}" required>
        <label class="correct-label">
            <input type="checkbox" class="is-correct" /> Correct Answer
        </label>
        <button type="button" class="delete-option" onclick="deleteOption(this)">
            <i class='bx bx-x'></i>
        </button>
    `;
    
    // Insert before the "Add Option" button
    container.insertBefore(optionDiv, button);
}

function deleteOption(button) {
    const option = button.closest('.option');
    const container = option.parentElement;
    const questionCard = option.closest('.question-card');
    const questionType = questionCard.querySelector('.question-type').value;
    
    if ((questionType === 'multiple_choice' || questionType === 'checkbox') && 
        container.querySelectorAll('.option').length <= 2) {
        alert('Multiple choice questions must have at least 2 options');
        return;
    }
    
    option.remove();
    
    // Renumber remaining options
    container.querySelectorAll('.option').forEach((optionDiv, index) => {
        const optionInput = optionDiv.querySelector('.option-input');
        const radioOrCheckbox = optionDiv.querySelector('input[type="radio"], input[type="checkbox"]');
        
        // Update placeholder text
        optionInput.placeholder = `Option ${index + 1}`;
        // Update value attribute
        radioOrCheckbox.value = index + 1;
    });
}

function deleteQuestion(button) {
    button.closest('.question-card').remove();
}

function saveQuiz() {
    const quiz = {
        title: document.getElementById('quizTitle')?.value || 'Untitled Quiz',
        description: document.getElementById('quizDescription')?.value || '',
        due_date: document.getElementById('quizDueDate')?.value || '',
        due_time: document.getElementById('quizDueTime')?.value || '',
        mode: document.getElementById('quizMode')?.value || 'individual',
        questions: []
    };

    if (!quiz.title.trim()) {
        showNotification('Quiz title is required.', 'error');
        return;
    }

    // Collect questions data
    const questionCards = document.querySelectorAll('.question-card');
    let isValid = true;

    questionCards.forEach((card, index) => {
        const questionType = card.querySelector('.question-type')?.value;
        const questionText = card.querySelector('.question-text')?.value;

        if (!questionType || !questionText?.trim()) {
            showNotification(`Please complete all fields for Question ${index + 1}`, 'error');
            isValid = false;
            return;
        }

        const question = {
            text: questionText,
            type: questionType,
            options: [],
            correct_answer: null,
            model_answer: null
        };

        switch (questionType) {
            case 'true_false':
                const selectedTF = card.querySelector('input[type="radio"]:checked');
                if (!selectedTF) {
                    showNotification(`Please select correct answer for Question ${index + 1}`, 'error');
                    isValid = false;
                    return;
                }
                question.correct_answer = selectedTF.value;
                break;

            case 'multiple_choice':
                const mcOptions = card.querySelectorAll('.option-item');
                if (mcOptions.length < 2) {
                    showNotification(`Question ${index + 1} must have at least 2 options`, 'error');
                    isValid = false;
                    return;
                }
                mcOptions.forEach(option => {
                    question.options.push({
                        text: option.querySelector('.option-text').value,
                        is_correct: option.querySelector('input[type="radio"]').checked
                    });
                });
                break;

            case 'multiple_answer':
                const maOptions = card.querySelectorAll('.option-item');
                if (maOptions.length < 2) {
                    showNotification(`Question ${index + 1} must have at least 2 options`, 'error');
                    isValid = false;
                    return;
                }
                maOptions.forEach(option => {
                    question.options.push({
                        text: option.querySelector('.option-text').value,
                        is_correct: option.querySelector('input[type="checkbox"]').checked
                    });
                });
                break;

            case 'short_answer':
                question.model_answer = card.querySelector('.model-answer-input')?.value;
                if (!question.model_answer?.trim()) {
                    showNotification(`Please provide a model answer for Question ${index + 1}`, 'error');
                    isValid = false;
                    return;
                }
                break;
        }

        quiz.questions.push(question);
    });

    if (!isValid) return;

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
            showNotification('Quiz saved successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'quiz.php';
            }, 1500);
        } else {
            showNotification(data.message || 'Error saving quiz', 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving quiz', 'error');
        console.error('Error:', error);
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
