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

    // Scroll to first error if any exist 
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
        // Debug data being sent
        const formData = new FormData();
        formData.append('quiz_id', form.dataset.quizId);
        
        // Add each question response
        const questions = form.querySelectorAll('.question-card');
        let responseCount = 0;
        
        questions.forEach(question => {
            const questionId = question.querySelector('input[type="radio"]')?.name.replace('q_', '');
            if (questionId) {
                const selectedAnswer = question.querySelector('input[type="radio"]:checked')?.value;
                if (selectedAnswer) {
                    formData.append(`responses[${questionId}]`, selectedAnswer);
                    responseCount++;
                }
            }
        });

        console.log('Submitting quiz:', {
            quizId: form.dataset.quizId,
            responseCount: responseCount
        });

        const response = await fetch('../actions/submit_quiz.php', {
            method: 'POST',
            body: formData
        });

        console.log('Response status:', response.status);
        const responseText = await response.text();
        console.log('Raw response:', responseText);

        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON response:', e);
            throw new Error('Server returned invalid JSON response');
        }

        if (result.success) {
            showNotification('Quiz submitted successfully!', 'success');
            // Redirect to results page after a brief delay
            setTimeout(() => {
                window.location.href = `quiz_result.php?id=${result.result_id}`;
            }, 1500);
        } else {
            throw new Error(result.message || 'Failed to submit quiz');
        }
    } catch (error) {
        console.error('Submission error:', error);
        showNotification(
            `Error submitting quiz: ${error.message}. Please try again or contact support if the problem persists.`,
            'error'
        );
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