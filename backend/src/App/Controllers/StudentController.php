<?php
// src/controllers/StudentController.php
namespace App\Controllers;

use App\db;
use PDO;
use PDOException;
use RuntimeException;

class StudentController {
    public function __construct(private db $database){}

    public function getAll() : array {
        try {
            $pdo = $this->database->getPDO();
            $stmt = $pdo->query('SELECT name,matric_no, email, phone, address, status FROM students');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch all students: " . $e->getMessage(), 0, $e);
        }
    }

    public function getStudentById(int $userId) {
        try {
            $pdo = $this->database->getPDO();

            // DEBUG LOG: Log the incoming userId
            error_log("DEBUG: getStudentById called with userId: " . $userId);

            // Step 1: Get the username from the 'users' table using the provided userId
            $userSql = "SELECT username FROM users WHERE id = :userId";
            $userStmt = $pdo->prepare($userSql);
            $userStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $userStmt->execute();
            $userResult = $userStmt->fetch(PDO::FETCH_ASSOC);

            // DEBUG LOG: Log the result of the user lookup
            if ($userResult) {
                error_log("DEBUG: User found in 'users' table. Username: " . $userResult['username']);
            } else {
                error_log("DEBUG: No user found in 'users' table for userId: " . $userId);
            }

            if (!$userResult || empty($userResult['username'])) {
                return false; // No corresponding user, so no student profile can be linked
            }

            $username = $userResult['username']; // This is the username we need to query the 'students' table

            // DEBUG LOG: Log the username obtained from the users table
            error_log("DEBUG: Retrieved username from 'users' table: " . $username);


            // Step 2: Use the retrieved username to get the student's details from the 'students' table
            $studentSql = "SELECT  ,matric_no, email, phone, address, status
                           FROM students
                           WHERE username = :username"; // Query the students table using the username (the foreign key)
            $studentStmt = $pdo->prepare($studentSql);
            $studentStmt->bindParam(':username', $username); // Bind the username from the users table
            $studentStmt->execute();
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC); // Fetch a single row

            // DEBUG LOG: Log the result of the student profile lookup
            if ($student) {
                error_log("DEBUG: Student profile found for username: " . $username . ". Data: " . json_encode($student));
            } else {
                error_log("DEBUG: No student profile found in 'students' table for username: " . $username);
            }

            return $student; // Will return false if no student record exists for that username

        } catch (PDOException $e) {
            // DEBUG LOG: Log any PDO exceptions
            error_log("ERROR: PDOException in getStudentById: " . $e->getMessage());
            throw new RuntimeException("Database error fetching student profile: " . $e->getMessage(), 0, $e);
        }
    }
}
