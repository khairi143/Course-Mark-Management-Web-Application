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

class AuthController{
    private string $jwtSecretKey;

    public function __construct(private db $database,private StudentService $studentService, string $jwtSecretKey){
        $this->jwtSecretKey = $jwtSecretKey;
    }

    public function register(array $registrationData) {
        $pdo = $this->database->getPDO();
        $pdo->beginTransaction();
        
        try {
            // 1. Core User Creation
            $username = $registrationData['username'];
            $password = $registrationData['password'];
            $role = $registrationData['role'] ?? 'student'; // Allow client to specify role, or default

            // Validate role against allowed enum values
            if (!in_array($role, ['admin', 'lecturer', 'student', 'advisor'])) {
                 throw new InvalidArgumentException("Invalid role specified.");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) { throw new \RuntimeException("Failed to hash password."); }

            $userSql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $userStmt = $pdo->prepare($userSql);
            $userStmt->bindParam(':username', $username);
            $userStmt->bindParam(':password', $hashedPassword);
            $userStmt->bindParam(':role', $role);
            $userStmt->execute();

            $newUserId = (int)$pdo->lastInsertId();

            // 2. Role-specific Profile Creation
            if ($role === 'student') {
                $profileData = $this->studentService->createStudentProfile($pdo, $username, $registrationData);
            } elseif ($role === 'lecturer') {
                // TODO: Implement lecturer profile creation
                // $profileData = $this->lecturerService->createLecturerProfile($pdo, $username, $registrationData);
            } elseif ($role === 'advisor') {
                // TODO: Implement advisor profile creation
                // $profileData = $this->advisorService->createAdvisorProfile($pdo, $username, $registrationData);
            }

            $pdo->commit();

            return [
                'user_id' => $newUserId,
                'username' => $username,
                'role' => $role,
                'profile_data' => $profileData, // Return profile-specific data if available
                'message' => 'User registered successfully.'
            ];

        } catch (PDOException | InvalidArgumentException | RuntimeException $e) {
            $pdo->rollBack();
            throw new RuntimeException("Registration failed: " . $e->getMessage(), 500, $e);
        }

    }

    public function login(array $credentials): array
    {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            throw new InvalidArgumentException("Username and password are required.");
        }

        $username = $credentials['username'];
        $password = $credentials['password'];

        try {
            $pdo = $this->database->getPDO();

            // 1. Find the user by username
            $sql = "SELECT id, username, password, role FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Verify the password
            /*if (!password_verify($password, $user['password'])) {
                throw new \RuntimeException("Invalid username or password.", 401); // 401 Unauthorized
            }*/

            if (!$user || !password_verify($password, $user['password'])) {
                error_log("LOGIN FAILED: Password mismatch");
                error_log("Entered password: " . $password);
                error_log("Stored hash: " . $user['password']);
                throw new RuntimeException("Invalid username or password.", 401);
              
            }

            // 3. Generate JWT if authentication is successful
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // Token valid for 1 hour (3600 seconds)

            $payload = [
                'iat'  => $issuedAt,             // Issued at: time when the token was generated
                'exp'  => $expirationTime,       // Expiration time
                'iss'  => 'your-app-domain.com', // Issuer (e.g., your domain)
                'data' => [                      // Custom data to include in the token
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ];

            // Encode the JWT
            $token = JWT::encode($payload, $this->jwtSecretKey, 'HS256');

            return [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ];

        } catch (PDOException $e) {
            throw new \RuntimeException("Database error during login: " . $e->getMessage(), 500, $e);
        } catch (\RuntimeException $e) {
            throw $e; // Re-throw authentication failures
        }
    }

    public function logout(): array
    {
      
        return ['message' => 'Logged out successfully.'];
    }
}