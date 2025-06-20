<!-- src/controllers/StudentController.php -->
<?php

namespace App\Controllers;

class StudentController {
    public function getAll($request, $response, $args) {
        global $pdo;  // Access the global PDO instance
        $stmt = $pdo->query("SELECT * FROM students");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $response->withJson($students);
    }
}
