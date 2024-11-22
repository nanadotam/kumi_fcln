document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quiz-form');
    const progressFill = document.getElementById('progressFill');
    const currentQuestionSpan = document.getElementById('currentQuestion');
    const totalQuestions = document.querySelectorAll('.question-card').length;
    
    function updateProgress() {
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked, textarea:not(:placeholder-shown)').length;
        const progress = (answeredQuestions / totalQuestions) * 100;
        progressFill.style.width = `${progress}%`;
        currentQuestionSpan.textContent = answeredQuestions;
    }
    
    // Listen for changes in radio buttons and textareas
    form.addEventListener('change', updateProgress);
    form.addEventListener('input', updateProgress);
    
    // Initial progress update
    updateProgress();
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const quizId = form.dataset.quizId;
        if (!quizId) {
            alert('Error: Quiz ID not found');
            return;
        }
        
        const formData = new FormData(form);
        const responses = {};
        
        formData.forEach((value, key) => {
            if (key.startsWith('q_')) {
                const questionId = key.split('_')[1];
                responses[questionId] = value;
            }
        });
        
        // Submit quiz with validation
        fetch('../actions/submit_quiz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                quiz_id: parseInt(quizId),
                responses: responses
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `quiz_result.php?id=${data.result_id}`;
            } else {
                alert('Error submitting quiz: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting quiz. Please try again.');
        });
    });
    
    // Add scroll behavior for progress bar
    const progressBar = document.querySelector('.quiz-progress-floating');
    let lastScrollTop = 0;
    let scrollTimeout;

    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Show/hide based on scroll direction
        if (scrollTop > lastScrollTop) {
            // Scrolling down
            progressBar.classList.remove('hidden');
            progressBar.style.animation = 'slideDown 0.3s ease forwards';
        } else if (scrollTop < 50) {
            // At the top
            progressBar.classList.add('hidden');
        }
        
        lastScrollTop = scrollTop;
        
        // Hide after 2 seconds of no scrolling
        scrollTimeout = setTimeout(() => {
            if (scrollTop > 50) {
                progressBar.classList.remove('hidden');
            }
        }, 2000);
    });

    // Show progress bar on mouse move
    document.addEventListener('mousemove', function(e) {
        if (e.clientY < 100) {
            progressBar.classList.remove('hidden');
            progressBar.style.animation = 'slideDown 0.3s ease forwards';
        }
    });
});  