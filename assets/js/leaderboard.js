class Leaderboard {
    constructor(quizCode) {
        this.quizCode = quizCode;
        this.updateInterval = 5000; // Update every 5 seconds
        this.init();
    }

    init() {
        // Initial load
        this.updateLeaderboard();
        
        // Set up periodic updates
        setInterval(() => this.updateLeaderboard(), this.updateInterval);
    }

    async updateLeaderboard() {
        try {
            const response = await fetch(`../api/leaderboard_api.php?code=${this.quizCode}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateUI(data);
            }
        } catch (error) {
            console.error('Error updating leaderboard:', error);
        }
    }

    updateUI(data) {
        // Update stats
        document.getElementById('participantCount').textContent = data.stats.total_participants;
        document.getElementById('averageScore').textContent = data.stats.average_score;

        // Update leaderboard
        const tbody = document.getElementById('leaderboardBody');
        tbody.innerHTML = ''; // Clear current entries

        data.rankings.forEach((entry, index) => {
            const row = document.createElement('tr');
            if (index < 3) row.classList.add('top-3');
            
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${entry.name}</td>
                <td>${entry.score}</td>
            `;
            
            tbody.appendChild(row);
        });
    }
} 