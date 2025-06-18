<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function createUser($data)
    {
        // Begin transaction
        $this->pdo->beginTransaction();

        try {
            // Insert into users table
            $stmt = $this->pdo->prepare("INSERT INTO users (username, full_name, role_id, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['username'],
                $data['fullName'],
                $data['role_id'],
                password_hash($data['password'], PASSWORD_DEFAULT)
            ]);

            // Get inserted user ID
            $userId = $this->pdo->lastInsertId();

            // If role is Lecturer (role_id = 1)
            if ($data['role_id'] == 1) {
                $dept = isset($data['department']) ? $data['department'] : '';
                $stmtLecturer = $this->pdo->prepare("INSERT INTO lecturers (user_id, department) VALUES (?, ?)");
                $stmtLecturer->execute([$userId, $dept]);
            }

            // If role is Student (e.g., role_id = 3)
            if ($data['role_id'] == 2) {
                $stmtStudent = $this->pdo->prepare("
                    INSERT INTO students (user_id, name, matric_number, email, phone, address)
                    VALUES (:user_id, :name, :matric_number, :email, :phone, :address)
                ");
                $stmtStudent->execute([
                    'user_id'       => $userId,
                    'name'          => $data['fullName'],
                    'matric_number' => $data['username'],
                    'email'         => $data['email'],
                    'phone'         => $data['phone'],
                    'address'       => $data['address'],
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
    }



    public function updateUser($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, full_name = ?, role_id = ? WHERE id = ?");
        return $stmt->execute([
            $data['username'],
            $data['fullName'],
            $data['role_id'],
            $id
        ]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function resetPasswordByUsername($username, $newPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $username
        ]);
    }

}
