document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    addQuestionBtn.addEventListener('click', function() {
        console.log('Add Question Button Clicked');
        addNewQuestion();
    });
    
    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
});

function addNewQuestion() {
    const questionsContainer = document.getElementById('questionsContainer');
    const questionHTML = `
        <div class="question-card">
            <div class="question-header">
                <input type="text" class="question-text" placeholder="Question">
                <select class="question-type" onchange="handleQuestionTypeChange(this)">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="paragraph">Paragraph</option>
                    <option value="checkbox">Select All That Apply</option>
                </select>
                <button class="btn-delete" onclick="deleteQuestion(this)">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
            <div class="options-container">
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
            </div>
        </div>
    `;
    
    questionsContainer.insertAdjacentHTML('beforeend', questionHTML);
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
                alert('Quiz saved successfully!');
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
