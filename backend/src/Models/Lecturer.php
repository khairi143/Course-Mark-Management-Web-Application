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
        $sql = "SELECT lecturers.id, users.full_name 
                FROM lecturers 
                JOIN users ON lecturers.user_id = users.id";
        $stmt = $this->pdo->query($sql);
        return $lecturers = $stmt->fetchAll();
    }
}
