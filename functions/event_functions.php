<?php
require_once '../utils/Database.php';

function logEvent($name, $description = null, $date = null) {
    $db = new Database();
    
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $sql = "INSERT INTO Events (name, description, date) VALUES (?, ?, ?)";
    return $db->query($sql, [$name, $description, $date]);
}

function getEventsByDate($startDate, $endDate) {
    $db = new Database();
    
    $sql = "SELECT * FROM Events 
            WHERE date BETWEEN ? AND ? 
            ORDER BY date DESC";
            
    $result = $db->query($sql, [$startDate, $endDate]);
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'event_id' => (int)$row['event_id'],
            'name' => htmlspecialchars($row['name']),
            'description' => htmlspecialchars($row['description']),
            'date' => $row['date']
        ];
    }
    
    return $events;
} 