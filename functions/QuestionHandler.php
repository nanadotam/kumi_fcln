<?php
class QuestionHandler {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function saveQuestion($quizId, $questionData) {
        $stmt = $this->db->prepare("
            INSERT INTO questions (quiz_id, question_text, question_type)
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([
            $quizId,
            $questionData['text'],
            $questionData['type']
        ]);
        
        $questionId = $this->db->lastInsertId();
        
        switch($questionData['type']) {
            case 'true_false':
                $this->saveTrueFalseAnswer($questionId, $questionData['correct_answer']);
                break;
            case 'multiple_choice':
            case 'multiple_answer':
                $this->saveOptions($questionId, $questionData['options']);
                break;
            case 'short_answer':
                $this->saveModelAnswer($questionId, $questionData['model_answer']);
                break;
        }
        
        return $questionId;
    }

    private function saveTrueFalseAnswer($questionId, $correctAnswer) {
        $stmt = $this->db->prepare("
            INSERT INTO question_options (question_id, option_text, is_correct)
            VALUES (?, 'true', ?), (?, 'false', ?)
        ");
        
        $stmt->execute([
            $questionId, 
            $correctAnswer === 'true',
            $questionId, 
            $correctAnswer === 'false'
        ]);
    }

    private function saveOptions($questionId, $options) {
        $stmt = $this->db->prepare("
            INSERT INTO question_options (question_id, option_text, is_correct)
            VALUES (?, ?, ?)
        ");
        
        foreach ($options as $option) {
            $stmt->execute([
                $questionId,
                $option['text'],
                $option['is_correct']
            ]);
        }
    }

    private function saveModelAnswer($questionId, $modelAnswer) {
        $stmt = $this->db->prepare("
            INSERT INTO short_answer_details (question_id, model_answer)
            VALUES (?, ?)
        ");
        
        $stmt->execute([$questionId, $modelAnswer]);
    }

    public function validateAnswer($questionId, $response) {
        $question = $this->getQuestionDetails($questionId);
        
        switch($question['question_type']) {
            case 'true_false':
                return $this->validateTrueFalse($questionId, $response);
            case 'multiple_choice':
                return $this->validateMultipleChoice($questionId, $response);
            case 'multiple_answer':
                return $this->validateMultipleAnswer($questionId, $response);
            case 'short_answer':
                return $this->validateShortAnswer($questionId, $response);
            default:
                return ['is_correct' => false, 'points' => 0];
        }
    }

    private function validateTrueFalse($questionId, $response) {
        $stmt = $this->db->prepare("
            SELECT is_correct 
            FROM question_options 
            WHERE question_id = ? AND option_text = ?
        ");
        
        $stmt->execute([$questionId, $response]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'is_correct' => (bool)$result['is_correct'],
            'points' => (bool)$result['is_correct'] ? 1 : 0
        ];
    }

    private function validateMultipleChoice($questionId, $response) {
        $stmt = $this->db->prepare("
            SELECT is_correct 
            FROM question_options 
            WHERE question_id = ? AND id = ?
        ");
        
        $stmt->execute([$questionId, $response]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'is_correct' => (bool)$result['is_correct'],
            'points' => (bool)$result['is_correct'] ? 1 : 0
        ];
    }

    private function validateMultipleAnswer($questionId, $responses) {
        if (!is_array($responses)) {
            return ['is_correct' => false, 'points' => 0];
        }

        // Get all correct answers
        $stmt = $this->db->prepare("
            SELECT id 
            FROM question_options 
            WHERE question_id = ? AND is_correct = 1
        ");
        
        $stmt->execute([$questionId]);
        $correctAnswers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Calculate partial credit
        $correctCount = count(array_intersect($responses, $correctAnswers));
        $incorrectCount = count(array_diff($responses, $correctAnswers));
        $totalCorrect = count($correctAnswers);
        
        $points = max(0, ($correctCount - $incorrectCount) / $totalCorrect);
        
        return [
            'is_correct' => $points === 1,
            'points' => $points
        ];
    }

    private function validateShortAnswer($questionId, $response) {
        $stmt = $this->db->prepare("
            SELECT model_answer 
            FROM short_answer_details 
            WHERE question_id = ?
        ");
        
        $stmt->execute([$questionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        similar_text(
            strtolower(trim($response)), 
            strtolower(trim($result['model_answer'])), 
            $similarity
        );
        
        return [
            'is_correct' => $similarity >= 80,
            'points' => $similarity / 100
        ];
    }

    private function getQuestionDetails($questionId) {
        $stmt = $this->db->prepare("
            SELECT question_type, points 
            FROM questions 
            WHERE id = ?
        ");
        
        $stmt->execute([$questionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 