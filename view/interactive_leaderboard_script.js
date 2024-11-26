const randomEmoji = () => {
    const emojis = ['👏','👍','🙌','🤩','🔥','⭐️','🏆','💯'];
    return emojis[Math.floor(Math.random() * emojis.length)];
};

// Function to calculate class statistics
const calculateClassStats = async () => {
    try {
        const response = await fetch('../api/class_statistics.php');
        const data = await response.json();
        
        if (data.success) {
            updateStatsDisplay(data.stats);
        }
    } catch (error) {
        console.error('Error fetching class statistics:', error);
    }
};

// Function to update the statistics display
const updateStatsDisplay = (stats) => {
    // Update average score card
    document.querySelector('.average-score-value').textContent = 
        `${stats.average_score}%`;
    
    // Update participation rate card (replacing quizzes completed)
    document.querySelector('.participation-rate-value').textContent = 
        `${stats.participation_rate}%`;
};

// Function to render leaderboard entries
const renderLeaderboardEntry = (student) => {
    const newRow = document.createElement('li');
    newRow.className = 'c-list__item';
    newRow.innerHTML = `
        <div class="c-list__grid">
            <div class="c-flag c-place u-bg--transparent">${student.rank}</div>
            <div class="c-media">
                <div class="c-media__content">
                    <div class="c-media__title">${student.name}</div>
                    <div class="u-text--small">${student.questions_answered} questions</div>
                </div>
            </div>
            <div class="u-text--right c-kudos">
                <div class="u-mt--8">
                    <strong>${student.total_score}</strong> ${randomEmoji()}
                </div>
            </div>
        </div>
    `;
    
    // Add special styling for top 3
    if (student.rank === 1) {
        newRow.querySelector('.c-place').classList.add('u-text--dark', 'u-bg--yellow');
        newRow.querySelector('.c-kudos').classList.add('u-text--yellow');
    } else if (student.rank === 2) {
        newRow.querySelector('.c-place').classList.add('u-text--dark', 'u-bg--teal');
        newRow.querySelector('.c-kudos').classList.add('u-text--teal');
    } else if (student.rank === 3) {
        newRow.querySelector('.c-place').classList.add('u-text--dark', 'u-bg--orange');
        newRow.querySelector('.c-kudos').classList.add('u-text--orange');
    }
    
    return newRow;
};

// Animation functions remain the same
const animatePositionChange = (element, oldPosition, newPosition) => {
    element.classList.add('position-changing');
    const distance = (newPosition - oldPosition) * elementHeight;
    element.style.transform = `translateY(${distance}px)`;
    
    setTimeout(() => {
        element.classList.remove('position-changing');
        element.style.transform = '';
    }, 500);
};

const animateScoreUpdate = (element, oldScore, newScore) => {
    element.classList.add('score-updating');
    const difference = newScore - oldScore;
    
    const changeIndicator = document.createElement('span');
    changeIndicator.className = `score-change ${difference > 0 ? 'positive' : 'negative'}`;
    changeIndicator.textContent = `${difference > 0 ? '+' : ''}${difference}`;
    
    element.appendChild(changeIndicator);
    
    setTimeout(() => {
        element.classList.remove('score-updating');
        changeIndicator.remove();
    }, 1000);
};

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    calculateClassStats();
});

