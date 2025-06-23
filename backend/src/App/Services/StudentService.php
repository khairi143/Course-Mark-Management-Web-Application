<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOException;
use InvalidArgumentException;

use RuntimeException;

class StudentService
{
    public function createStudentProfile(PDO $pdo, string $username, array $studentData): array
    {
        // 1. Validate required fields
        $requiredFields = [ 'name','matric_number', 'email'];

        foreach ($requiredFields as $field) {
            if (empty($studentData[$field])) {
                throw new InvalidArgumentException("Missing required student profile field: '$field'.");
            }
        }

        // 2. Extract fields from data
        $fullName = $studentData['name'];
        $matricNo = $studentData['matric_number'];
        $email = $studentData['email'];
        // $phone = $studentData['phone'];
        // $address = $studentData['address'];
        $status = 'Active'; // Default status

        try {
            // 3. Insert into `students` table
            $sql = "INSERT INTO students (user_Id, name, matric_number, email, status)
                    VALUES (:user_id, :name, :matric_number, :email,:status)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $studentData['user_id'],
                ':name' => $fullName,
                ':matric_number' => $matricNo,
                ':email' => $email,
                // ':phone' => $phone,
                // ':address' => $address,
                ':status' => $status
            ]);

            return [
                'message' => 'Student profile created successfully.',
                'matric_no' => $matricNo,
                'email' => $email
            ];

        } catch (PDOException $e) {
            // Handle duplicate Matric No
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'matric_no')) {
                throw new RuntimeException("Student registration failed: Matric number already exists.", 409, $e);
            }

            throw new RuntimeException("Database error while creating student profile: " . $e->getMessage(), 500, $e);
        }
    }
}
