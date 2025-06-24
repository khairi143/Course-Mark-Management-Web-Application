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

            // If role is Lecturer
            if ($data['role_id'] == 1) {
                $dept = isset($data['department']) ? $data['department'] : '';
                $stmtLecturer = $this->pdo->prepare("INSERT INTO lecturers (user_id, department) VALUES (?, ?)");
                $stmtLecturer->execute([$userId, $dept]);
            }

            // If role is Student 
            if ($data['role_id'] == 2) {
                $stmtStudent = $this->pdo->prepare("
                    INSERT INTO students (user_id, name, matric_number, email, phone, address, advisor_id)
                    VALUES (:user_id, :name, :matric_number, :email, :phone, :address, :advisor_id)
                ");
                $stmtStudent->execute([
                    'user_id'       => $userId,
                    'name'          => $data['fullName'],
                    'matric_number' => $data['username'],
                    'email'         => $data['email'],
                    'phone'         => $data['phone'],
                    'address'       => $data['address'],
                    'advisor_id'       => $data['advisor_id'],
                ]);
            }

            if ($data['role_id'] == 3) {
                $stmtAdvisor = $this->pdo->prepare("
                    INSERT INTO advisors (user_id, advisor_full_name)
                    VALUES (:user_id, :name)
                ");
                $stmtAdvisor->execute([
                    'user_id'       => $userId,
                    'name'          => $data['fullName'],
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
        $stmt->execute([$data['username'], $data['fullName'], $data['role_id'], $id]);

        // Handle student-specific data
        if ($data['role_id'] == 2) {
            $existsStmt = $this->pdo->prepare("SELECT id FROM students WHERE user_id = ?");
            $existsStmt->execute([$id]);

            if ($existsStmt->fetch()) {
                $stmtStudent = $this->pdo->prepare("UPDATE students SET name = ?, phone = ?, email = ?, address = ?, advisor_id = ? WHERE user_id = ?");
                $stmtStudent->execute([
                    $data['fullName'], $data['phone'], $data['email'], $data['address'], $data['advisor_id'], $id
                ]);
            } else {
                $stmtStudent = $this->pdo->prepare("INSERT INTO students (user_id, name, phone, email, address, advisor_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtStudent->execute([
                    $id, $data['fullName'], $data['phone'], $data['email'], $data['address'], $data['advisor_id']
                ]);
            }
        }

        // Handle lecturer-specific data
        if ($data['role_id'] == 1) {
            $existsStmt = $this->pdo->prepare("SELECT id FROM lecturers WHERE user_id = ?");
            $existsStmt->execute([$id]);

            if ($existsStmt->fetch()) {
                $stmtLecturer = $this->pdo->prepare("UPDATE lecturers SET department = ? WHERE user_id = ?");
                $stmtLecturer->execute([
                    $data['department'], $id
                ]);
            } else {
                $stmtLecturer = $this->pdo->prepare("INSERT INTO lecturers (user_id, department) VALUES (?, ?)");
                $stmtLecturer->execute([
                    $id, $data['department']
                ]);
            }
        }

        return true;
    }


    public function deleteUser($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Step 1: Get role_id for the user
            $stmtRole = $this->pdo->prepare("SELECT role_id FROM users WHERE id = ?");
            $stmtRole->execute([$id]);
            $role = $stmtRole->fetchColumn();

            if (!$role) {
                $this->pdo->rollBack();
                return false; // User not found
            }

            // Step 2: Delete from role-specific table
            if ($role == 1) {
                // Lecturer
                $stmt = $this->pdo->prepare("DELETE FROM lecturers WHERE user_id = ?");
                $stmt->execute([$id]);
            } elseif ($role == 2) {
                // Student
                $stmt = $this->pdo->prepare("DELETE FROM students WHERE user_id = ?");
                $stmt->execute([$id]);
            } elseif ($role == 3) {
                // Advisor
                $stmt = $this->pdo->prepare("DELETE FROM advisors WHERE user_id = ?");
                $stmt->execute([$id]);
            }

            // Step 3: Delete from users table
            $stmtDeleteUser = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmtDeleteUser->execute([$id]);

            $this->pdo->commit();
            return true;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("User deletion failed: " . $e->getMessage());
            return false;
        }
    }


    public function resetPasswordByUsername($username, $newPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        return $stmt->execute([
            $newPassword,
            $username
        ]);
    }

    public function getUserByUsername($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getRoleByUsername($username)
    {
        $sql = "SELECT r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = :username";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAllUsersWithDetails()
    {
        $stmt = $this->pdo->query("
            SELECT 
                u.*, 
                s.phone, s.email, s.address, s.advisor_id, 
                l.department 
            FROM users u
            LEFT JOIN students s ON u.id = s.user_id
            LEFT JOIN lecturers l ON u.id = l.user_id
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }



}
