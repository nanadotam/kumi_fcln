<?php
require_once '../utils/Database.php';

function createGroup($groupName, $createdBy) {
    $db = new Database();
    
    $sql = "INSERT INTO Groups (group_name) VALUES (?)";
    $result = $db->query($sql, [$groupName]);
    
    if ($result) {
        $groupId = $db->getConnection()->insert_id;
        // Add creator as first member
        addGroupMember($groupId, $createdBy);
        return $groupId;
    }
    return false;
}

function addGroupMember($groupId, $userId) {
    $db = new Database();
    
    $sql = "INSERT INTO GroupMembers (group_id, user_id) VALUES (?, ?)";
    return $db->query($sql, [$groupId, $userId]);
}

function getGroupMembers($groupId) {
    $db = new Database();
    
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email 
            FROM Users u 
            JOIN GroupMembers gm ON u.user_id = gm.user_id 
            WHERE gm.group_id = ?";
            
    $result = $db->query($sql, [$groupId]);
    
    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = [
            'user_id' => (int)$row['user_id'],
            'name' => htmlspecialchars($row['first_name'] . ' ' . $row['last_name']),
            'email' => htmlspecialchars($row['email'])
        ];
    }
    
    return $members;
} 