
<?php

namespace App\Models;

class Student {
    private $user_id; 
    private $name;
    private $matric_number;
    private $email;
    private $phone;
    private $address;
    private $status;

    // Define getter and setter methods

    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM students");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
