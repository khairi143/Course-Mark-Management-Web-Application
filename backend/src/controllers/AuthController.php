<?php

namespace App\controllers;

use App\db;
use Firebase\JWT\JWT;
use RuntimeException;

class AuthController
{
    private $db;
    private $secretKey;

    public function __construct(db $db, string $secretKey)
    {
        $this->db = $db;
        $this->secretKey = $secretKey;
    }

    public function register(array $data): array
    {
        $username = $data['username'] ?? '';
        $fullname = $data['full_name'] ?? '';
        $password = $data['password'] ?? '';
        $role     = $data['role_name'] ?? '';

        // Student-specific fields
        $name          = $data['name'] ?? '';
        $matric_number = $data['matric_number'] ?? '';
        $email         = $data['email'] ?? '';
        $phone         = $data['phone'] ?? '';
        $address       = $data['address'] ?? '';

        if (!$username || !$fullname || !$password || !$role) {
            throw new \InvalidArgumentException("Missing user fields.");
        }

        $pdo = $this->db->connect();

        // Check if username exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new RuntimeException("Username already exists.", 409);
        }

        // Insert into users table
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, role_name, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$username, $fullname, $hashedPassword, $role]);

        $userId = $pdo->lastInsertId();

        // Only insert into students table if role_id == 2 (student)
        if ((int)$role === 2) {
            if (!$name || !$matric_number || !$email) {
                throw new \InvalidArgumentException("Missing student-specific fields.");
            }

            $stmt = $pdo->prepare("INSERT INTO students (user_id, name, matric_number, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $name,
                $matric_number,
                $email,
                $phone,
                $address
            ]);
        }

        return ['message' => 'User registered successfully'];
    }

    public function login(array $credentials): array
    {
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';

        if (!$username || !$password) {
            throw new \InvalidArgumentException("Username and password are required.");
        }

        $pdo = $this->db->connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new RuntimeException("Invalid credentials", 401);
        }

        $issuedAt = time();
        $expire = $issuedAt + 3600;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role_id']
            ]
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256');

        return ['token' => $token];
    }

    public function logout(): array
    {
        return ['message' => 'Logged out (client should discard the token)'];
    }
}
