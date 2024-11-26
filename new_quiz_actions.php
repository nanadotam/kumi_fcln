<?php
function createQuiz($quizData) {
    $conn = getConnection();
    
    $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", 
        $quizData['title'],
        $quizData['description'],
        $quizData['created_by'],
        $quizData['mode'],
        $quizData['deadline'],
        $quizData['created_at']
    );
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}
?>
