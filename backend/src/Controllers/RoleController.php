<?php

namespace App\Controllers;

use App\Models\Role;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RoleController
{
    private $roleModel;

    public function __construct()
    {
        $this->roleModel = new Role();
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        $roles = $this->roleModel->getAllRoles();
        $response->getBody()->write(json_encode($roles));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
