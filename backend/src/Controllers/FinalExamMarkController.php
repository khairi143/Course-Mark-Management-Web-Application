<?php
namespace App\Controllers;

use App\Models\FinalExamMark;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FinalExamMarkController {
    private $model;

    public function __construct() {
        $this->model = new FinalExamMark();
    }

    public function getAll(Request $request, Response $response): Response {
        $data = $this->model->getAll();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $request, Response $response, $args): Response {
        $data = $this->model->getById($args['id']);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getByCourse(Request $request, Response $response, $args): Response {
        $data = $this->model->getByCourse($args['course_id']);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $success = $this->model->create($body['student_id'], $body['course_id'], $body['marks_obtained']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args): Response {
        $body = $request->getParsedBody();
        $success = $this->model->update($args['id'], $body['marks_obtained']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args): Response {
        $success = $this->model->delete($args['id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
