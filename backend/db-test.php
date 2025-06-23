<?php
// Include the database connection function
require_once 'db.php';

// Set CORS headers if the frontend and backend are on different domains
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// SQL query to fetch user data
$sql = "SELECT id, username, full_name, role_id, created_at, updated_at FROM users";

try {
    // Get the PDO instance from your getPDO() function (in db.php)
    $stmt = getPDO()->prepare($sql);
    $stmt->execute();
    
    // Fetch all users as an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the data as a JSON response
    echo json_encode($users);
} catch (PDOException $e) {
    // If there's an error with the database query
    echo json_encode(['error' => 'Error fetching users: ' . $e->getMessage()]);
}
?>