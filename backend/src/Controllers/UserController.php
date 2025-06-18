<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        $users = $this->userModel->getAllUsers();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $this->userModel->createUser($data);
        $response->getBody()->write(json_encode(['message' => 'User created']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $this->userModel->updateUser($id, $data);
        $response->getBody()->write(json_encode(['message' => 'User updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $this->userModel->deleteUser($id);
        $response->getBody()->write(json_encode(['message' => 'User deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function resetPassword(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
    
        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode([
                'error' => 'Username and new password are required'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $userModel = new User();
        $result = $userModel->resetPasswordByUsername($username, $password);
    
        if ($result) {
            $response->getBody()->write(json_encode([
                'message' => 'Password reset successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'User not found or update failed'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }

}
