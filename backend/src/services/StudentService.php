<?php

namespace App\Services;

use App\db;

class StudentService
{
    private $db;

    public function __construct(db $db)
    {
        $this->db = $db;
    }

    public function createStudent(array $data): void
    {
        $pdo = $this->db->connect();
        $stmt = $pdo->prepare("INSERT INTO STUDENTS (user_id, name, matric_number, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['matric_number'],
            $data['email'],
            $data['phone'],
            $data['address']
        ]);
    }
}
