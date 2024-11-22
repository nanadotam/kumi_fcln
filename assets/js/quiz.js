document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');

    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', function () {
            console.log('Add Question Button Clicked');
            addNewQuestion();
        });
    }

    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
});

let questionCounter = 0; // To track unique question IDs

function addNewQuestion() {
    questionCounter++;
    const questionsContainer = document.getElementById('questionsContainer');
    if (!questionsContainer) {
        console.error('Questions container not found');
        return;
    }

    const questionHTML = `
        <div class="question-card" data-id="question-${questionCounter}">
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
                <!-- Default options -->
                <button class="btn-add" onclick="addOption(this.parentElement, 'radio')">
                    <i class='bx bx-plus'></i> Add Option
                </button>
            </div>
        </div>
    `;

    questionsContainer.insertAdjacentHTML('beforeend', questionHTML);
    createMultipleChoiceOptions(questionsContainer.querySelector(`[data-id="question-${questionCounter}"] .options-container`));
}

function saveQuiz() {
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
