<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Student;

class StudentAssessmentMarkController
{
    private $studentModel;

    public function __construct()
    {
        $this->studentModel = new Student();
    }

    public function getAll(Request $request, Response $response, $args)
    {
        $students = $this->studentModel->getAll();
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $result = $this->studentModel->create($data);
        $response->getBody()->write(json_encode(['message' => 'Student added']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $this->studentModel->update($id, $data);
        $response->getBody()->write(json_encode(['message' => 'Student updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $this->studentModel->delete($id);
        $response->getBody()->write(json_encode(['message' => 'Student deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
