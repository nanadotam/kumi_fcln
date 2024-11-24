class LiveQuizController {
    constructor(quizCode) {
        this.quizCode = quizCode;
        this.participants = new Map();
        this.currentQuestion = 0;
        this.status = 'waiting';
    }

    startQuiz() {
        this.status = 'active';
        this.broadcastQuestion();
    }

    simulateParticipant(studentName) {
        const studentId = Date.now();
        this.participants.set(studentId, {
            name: studentName,
            score: 0,
            answers: []
        });
        return studentId;
    }
} 