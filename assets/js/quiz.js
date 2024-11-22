let questionCount = 0;

document.getElementById('add-question-btn').addEventListener('click', addQuestion);

function addQuestion() {
    questionCount++;

    const quizContainer = document.getElementById('quiz-container');
    
    const questionContainer = document.createElement('div');
    questionContainer.classList.add('question-container');
    questionContainer.id = `question-${questionCount}`;

    questionContainer.innerHTML = `
        <label>Question ${questionCount}</label>
        <input type="text" placeholder="Enter question text" class="question-text">
        
        <label>Answer Type</label>
        <select class="answer-type" onchange="toggleAnswerType(this, ${questionCount})">
            <option value="paragraph">Paragraph</option>
            <option value="multiple-choice">Multiple Choice</option>
            <option value="check-box">Check Box</option> 
        </select>

        <div class="multiple-choice-options" id="mc-options-${questionCount}" style="display: none;">
            <label>Options</label>
            <button class="add-option-btn" onclick="addOption(${questionCount}, 'multiple-choice')">Add Option</button>
            <div class="options-container"></div>
        </div>
        
        <div class="check-box-options" id="cb-options-${questionCount}" style="display: none;">
            <label>Options</label>
            <button class="add-option-btn" onclick="addOption(${questionCount}, 'check-box')">Add Option</button>
            <div class="options-container"></div>
        </div>

        <label>
            <input type="checkbox" class="required-checkbox">
            Required
        </label>

        <label>Points</label>
        <input type="number" class="points-input" min="0" placeholder="Enter points">

        <button class="remove-question-btn" onclick="removeQuestion(${questionCount})">Remove Question</button>
    `;

    quizContainer.appendChild(questionContainer);
}

function toggleAnswerType(selectElement, questionId) {
    const mcOptionsContainer = document.getElementById(`mc-options-${questionId}`);
    const cbOptionsContainer = document.getElementById(`cb-options-${questionId}`);
    
    mcOptionsContainer.style.display = selectElement.value === 'multiple-choice' ? 'block' : 'none';
    cbOptionsContainer.style.display = selectElement.value === 'check-box' ? 'block' : 'none';
}

function addOption(questionId, type) {
    const optionsContainer = document.querySelector(`#question-${questionId} .${type}-options .options-container`);

    const optionInput = document.createElement('div');
    optionInput.classList.add('option-input');

    if (type === 'multiple-choice') {
        optionInput.innerHTML = `
            <input type="radio" name="question-${questionId}-option" disabled>
            <input type="text" placeholder="Option text">
            <button class="remove-option-btn" onclick="this.parentElement.remove()">Remove Option</button>
        `;
    } else if (type === 'check-box') {
        optionInput.innerHTML = `
            <input type="checkbox" disabled>
            <input type="text" placeholder="Option text">
            <button class="remove-option-btn" onclick="this.parentElement.remove()">Remove Option</button>
        `;
    }
    
    optionsContainer.appendChild(optionInput);
}

function removeQuestion(questionId) {
    const questionContainer = document.getElementById(`question-${questionId}`);
    questionContainer.remove();
}

document.getElementById('preview-btn').addEventListener('click', previewQuiz);
document.getElementById('view-responses-btn').addEventListener('click', viewResponses);

function previewQuiz() {
    // Get all questions
    const questions = document.querySelectorAll('.question-container');
    const previewHTML = [];
    
    // Create preview modal
    const modal = document.createElement('div');
    modal.className = 'preview-modal';
    
    questions.forEach((question, index) => {
        const questionText = question.querySelector('.question-text').value;
        const answerType = question.querySelector('.answer-type').value;
        const required = question.querySelector('.required-checkbox').checked;
        const points = question.querySelector('.points-input').value;
        
        let optionsHTML = '';
        if (answerType !== 'paragraph') {
            const options = question.querySelectorAll('.option-input input[type="text"]');
            options.forEach(option => {
                const inputType = answerType === 'multiple-choice' ? 'radio' : 'checkbox';
                optionsHTML += `
                    <div class="preview-option">
                        <input type="${inputType}" name="q${index}" disabled>
                        <label>${option.value}</label>
                    </div>
                `;
            });
        }
        
        previewHTML.push(`
            <div class="preview-question">
                <h3>Question ${index + 1} ${required ? '*' : ''} (${points} points)</h3>
                <p>${questionText}</p>
                ${answerType === 'paragraph' 
                    ? '<textarea disabled placeholder="Student answer here..."></textarea>'
                    : optionsHTML
                }
            </div>
        `);
    });
    
    modal.innerHTML = `
        <div class="preview-content">
            <h2>Quiz Preview</h2>
            ${previewHTML.join('')}
            <button onclick="closePreview()">Close Preview</button>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function closePreview() {
    document.querySelector('.preview-modal').remove();
}

function viewResponses() {
    alert("Check responses to your quiz here! (Feature coming soon!)");
   //This could also navigate to a new page or maybe display a modal or something, tbd
}

document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality for teacher view
    const searchInput = document.getElementById('search-quiz');
    const modeFilter = document.getElementById('filter-mode');
    
    if (searchInput && modeFilter) {
        const quizCards = document.querySelectorAll('.quiz-card');

        function filterQuizzes() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedMode = modeFilter.value;

            quizCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const mode = card.dataset.mode;
                const matchesSearch = title.includes(searchTerm);
                const matchesMode = !selectedMode || mode === selectedMode;

                card.style.display = matchesSearch && matchesMode ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', filterQuizzes);
        modeFilter.addEventListener('change', filterQuizzes);
    }
});

function editQuiz(quizId) {
    window.location.href = `create_quiz.php?id=${quizId}`;
}

function deleteQuiz(quizId) {
    if (confirm('Are you sure you want to delete this quiz?')) {
        fetch('../actions/delete_quiz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ quiz_id: quizId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete quiz');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const quizForm = document.getElementById('quiz-form');
    
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateQuizResponses()) {
                submitQuiz(this);
            }
        });
    }
});

function validateQuizResponses() {
    const questions = document.querySelectorAll('.question-card');
    let isValid = true;
    let firstError = null;

    questions.forEach(question => {
        // Clear previous error states
        question.classList.remove('error');
        const errorMsg = question.querySelector('.error-message');
        if (errorMsg) errorMsg.remove();

        // Get question inputs
        const inputs = question.querySelectorAll('input[type="radio"]');
        const required = inputs[0]?.hasAttribute('required');

        // Check if any option is selected for required questions
        if (required && ![...inputs].some(input => input.checked)) {
            isValid = false;
            question.classList.add('error');
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = 'Please select an answer for this question';
            question.appendChild(errorDiv);

            // Store first error for scrolling
            if (!firstError) firstError = question;
        }
    });

    // Scroll to first error if any
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return isValid;
}

async function submitQuiz(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Submitting...';

    try {
        // Collect all responses
        const formData = new FormData();
        formData.append('quiz_id', form.dataset.quizId);
        
        // Add each question response
        const questions = form.querySelectorAll('.question-card');
        questions.forEach(question => {
            const questionId = question.querySelector('input[type="radio"]')
                                    ?.name.replace('q_', '');
            if (questionId) {
                const selectedAnswer = question.querySelector('input[type="radio"]:checked')?.value;
                if (selectedAnswer) {
                    formData.append(`responses[${questionId}]`, selectedAnswer);
                }
            }
        });

        const response = await fetch('../actions/submit_quiz.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Quiz submitted successfully!', 'success');
            // Redirect to results page after a brief delay
            setTimeout(() => {
                window.location.href = `quiz_result.php?id=${result.score}`;
            }, 1500);
        } else {
            throw new Error(result.message || 'Failed to submit quiz');
        }
    } catch (error) {
        showNotification(error.message, 'error');
        submitButton.disabled = false;
        submitButton.textContent = 'Submit Quiz';
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
