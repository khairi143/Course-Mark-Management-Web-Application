<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
require_once __DIR__ . '/../../config/database.php';

class AuthController
{
    private $userModel;
    private $jwtSecret;
    private $pdo;


    public function __construct()
    {
        $this->userModel = new User();
        $this->jwtSecret = 'my-secret-key';
        $this->pdo = getPDO();
    }

    public function loginStaff(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode(['error' => 'Username and password are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = $this->userModel->getUserByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Only allow staff roles (not students)
        if ($user['role_id'] == 2) {
            $response->getBody()->write(json_encode(['error' => 'Not a staff account']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $issuedAt = time();
        $expire = $issuedAt + 3600;
        $payload = [
            'user' => $user['username'],
            'role' => $user['role_id'],
            'iat' => $issuedAt,
            'exp' => $expire
        ];
        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');
        $this->logLoginAction($user['id']);

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role_id' => $user['role_id']
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function loginStudent(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $matricNo = $data['matricNo'] ?? '';
        $pin = $data['pin'] ?? '';

        if (empty($matricNo) || empty($pin)) {
            $response->getBody()->write(json_encode(['error' => 'Matric No and PIN are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = $this->userModel->getUserByUsername($matricNo);
        if (!$user || !password_verify($pin, $user['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Only allow student role
        if ($user['role_id'] != 2) {
            $response->getBody()->write(json_encode(['error' => 'Not a student account']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $issuedAt = time();
        $expire = $issuedAt + 3600;
        $payload = [
            'user' => $user['username'],
            'role' => $user['role_id'],
            'iat' => $issuedAt,
            'exp' => $expire
        ];
        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');
        $this->logLoginAction($user['id']);

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'matric_no' => $user['username'],
                'full_name' => $user['full_name'],
                'role_id' => $user['role_id']
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function logLoginAction($userId, $action = 'login')
    {
       
        $stmt = $this->pdo->prepare("INSERT INTO logs (user_id, action, timestamp) VALUES (:user_id, :action, NOW())");
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action
        ]);
    }

}
