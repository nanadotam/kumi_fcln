document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const questionsContainer = document.getElementById('questionsContainer');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const saveQuizBtn = document.getElementById('saveQuizBtn');
    
    let questionCount = 0;

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
                <label>Question Type</label>
                <select class="question-type" onchange="handleQuestionTypeChange(this)">
                    <option value="">Select question type...</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="checkbox">Multiple Answer</option>
                    <option value="paragraph">Short Answer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Points</label>
                <input type="number" class="question-points" min="1" value="1" required>
            </div>
            <div class="options-container">
                <!-- Options will be added here based on question type -->
            </div>
        `;
        questionsContainer.appendChild(questionDiv);
    }

    function addOption(button, type) {
        const container = button.parentElement.querySelector('.options-container');
        const optionCount = container.querySelectorAll('.option').length + 1;
        const questionNumber = button.closest('.question-card').querySelector('.question-header h3').textContent.split(' ')[1];
        
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option';
        optionDiv.innerHTML = `
            <input type="${type}" name="correct_${questionNumber}" value="${optionCount - 1}">
            <input type="text" class="option-input" placeholder="Option ${optionCount}" required>
            <label class="correct-label">
                <input type="checkbox" class="is-correct" /> Correct Answer
            </label>
            <button type="button" class="delete-option" onclick="deleteOption(this)">
                <i class='bx bx-x'></i>
            </button>
        `;
        
        container.insertBefore(optionDiv, button);
    }

    function deleteOption(button) {
        const optionItem = button.closest('.option');
        const container = optionItem.parentElement;
        optionItem.remove();
        
        container.querySelectorAll('.option').forEach((item, index) => {
            const input = item.querySelector('.option-input');
            if (input) {
                input.placeholder = `Option ${index + 1}`;
            }
        });
    }

    function deleteQuestion(button) {
        const question = button.closest('.question-card');
        question.remove();
        updateQuestionNumbers();
    }

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

    function saveQuiz() {
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

        let isValid = true;
        document.querySelectorAll('.question-card').forEach((card, questionIndex) => {
            const questionText = card.querySelector('.question-text')?.value.trim();
            const questionType = card.querySelector('.question-type')?.value;
            const optionsContainer = card.querySelector('.options-container');
            
            if (!questionText || !questionType) {
                alert(`Question ${questionIndex + 1}: Text and type are required`);
                isValid = false;
                return;
            }

            if (questionType === 'multiple_choice' || questionType === 'checkbox') {
                const options = optionsContainer.querySelectorAll('.option');
                
                if (options.length < 2) {
                    alert(`Question ${questionIndex + 1}: Multiple choice questions must have at least 2 options`);
                    isValid = false;
                    return;
                }

                let hasCorrectAnswer = false;
                options.forEach(option => {
                    if (option.querySelector('.is-correct').checked) {
                        hasCorrectAnswer = true;
                    }
                });

                if (!hasCorrectAnswer) {
                    alert(`Question ${questionIndex + 1}: Please select at least one correct answer`);
                    isValid = false;
                    return;
                }
            }

            const question = {
                text: questionText,
                type: questionType,
                points: card.querySelector('.question-points')?.value || 1,
                options: []
            };

            if (questionType === 'paragraph') {
                const modelAnswer = card.querySelector('.model-answer')?.value.trim();
                question.model_answer = modelAnswer;
            } else {
                optionsContainer.querySelectorAll('.option').forEach(option => {
                    question.options.push({
                        text: option.querySelector('.option-input').value.trim(),
                        is_correct: option.querySelector('.is-correct').checked
                    });
                });
            }

            quiz.questions.push(question);
        });

        if (!isValid) return;

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

    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', createQuestion);
    }

    if (saveQuizBtn) {
        saveQuizBtn.addEventListener('click', saveQuiz);
    }
});

