function takeQuiz(quizId) {
    const code = prompt('Please enter the quiz code to proceed:');
    
    if (code) {
        // Verify the code
        fetch('../actions/verify_quiz_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quiz_id: quizId,
                quiz_code: code
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `take_quiz.php?id=${quizId}`;
            } else {
                alert(data.message || 'Invalid quiz code. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }