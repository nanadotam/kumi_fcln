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
