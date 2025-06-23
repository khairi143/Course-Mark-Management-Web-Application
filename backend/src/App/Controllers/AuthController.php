<?php

declare(strict_types=1);

namespace App\Controllers;

use App\db;
use App\Services\StudentService;
use PDO;
use PDOException;
use InvalidArgumentException;
use RuntimeException;
use Firebase\JWT\JWT;

class AuthController {
    private string $jwtSecretKey;

    public function __construct(private db $database, private StudentService $studentService, string $jwtSecretKey) {
        $this->jwtSecretKey = $jwtSecretKey;
    }

    public function register(array $registrationData) {
        $pdo = $this->database->getPDO();
        $pdo->beginTransaction();

        try {
            // 1. Core User Creation
            $username = $registrationData['username'];
            $password = $registrationData['password'];
            $role_id = $registrationData['role_id']; // directly use integer

            if (!in_array($role_id, [1, 2, 3, 4])) {
                throw new InvalidArgumentException("Invalid role_id specified.");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                throw new RuntimeException("Failed to hash password.");
            }

            $userSql = "INSERT INTO users (username, password, role_id) VALUES (:username, :password, :role_id)";
            $userStmt = $pdo->prepare($userSql);
            $userStmt->bindParam(':username', $username);
            $userStmt->bindParam(':password', $hashedPassword);
            $userStmt->bindParam(':role_id', $role_id);
            $userStmt->execute();

            $newUserId = (int)$pdo->lastInsertId();
            $registrationData['user_id'] = $newUserId;

            // 2. Role-specific Profile Creation
            $profileData = null;
            if ($role_id === 2) {
                $profileData = $this->studentService->createStudentProfile($pdo, $username, $registrationData);
            }

            $pdo->commit();

            return [
                'user_id' => $newUserId,
                'username' => $username,
                'role_id' => $role_id,
                'profile_data' => $profileData,
                'message' => 'User registered successfully.'
            ];

        } catch (PDOException | InvalidArgumentException | RuntimeException $e) {
            $pdo->rollBack();
            throw new RuntimeException("Registration failed: " . $e->getMessage(), 500, $e);
        }
    }

    public function login(array $credentials): array {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            throw new InvalidArgumentException("Username and password are required.");
        }

        $username = $credentials['username'];
        $password = $credentials['password'];

        try {
            $pdo = $this->database->getPDO();

            $sql = "SELECT user_id, username, password, role_id FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                throw new RuntimeException("Invalid username or password.", 401);
            }

            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'iss' => 'your-app-domain.com',
                'data' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id']
                ]
            ];

            $token = JWT::encode($payload, $this->jwtSecretKey, 'HS256');

            return [
                'token' => $token,
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id']
                ]
            ];

        } catch (PDOException $e) {
            throw new RuntimeException("Database error during login: " . $e->getMessage(), 500, $e);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function logout(): array {
        return ['message' => 'Logged out successfully.'];
    }
}
