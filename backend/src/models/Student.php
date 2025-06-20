<!-- src/models/Student.php -->
<?php

namespace App\Models;

class Student {
    private $student_id;
    private $matric_no;
    private $student_name;
    private $email;
    private $status;

    // Define getter and setter methods

    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM students");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
