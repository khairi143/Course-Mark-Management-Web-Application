<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Student
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM students ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO students (user_id, name, matric_number, email, phone, address)
            VALUES (:user_id, :name, :matric_number, :email, :phone, :address)
        ");
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'name'       => $data['name'],
            'matric_number'      => $data['matric_number'],
            'email'     => $data['email'],
            'phone'       => $data['phone'],
            'address'       => $data['address'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE students
            SET user_id = :user_id,
                name = :name,
                matric_number = :matric_number,
                email = :email,
                phone = :phone,
                address = :address
            WHERE id = :id
        ");
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'name'       => $data['name'],
            'matric_number'      => $data['matric_number'],
            'email'     => $data['email'],
            'phone'       => $data['phone'],
            'address'       => $data['address'],
            'id'         => $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getIdByUserId($userId)
{
    $stmt = $this->pdo->prepare("SELECT id FROM students WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetch();
}

}
