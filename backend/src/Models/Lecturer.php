<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Lecturer
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAll()
    {
        $sql = "SELECT lecturers.*, users.full_name 
                FROM lecturers 
                JOIN users ON lecturers.user_id = users.id";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getLecturerIdByUserId($user_id)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM lecturers WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        $row = $stmt->fetch();
        return $row ? $row['id'] : null;
    }
}
