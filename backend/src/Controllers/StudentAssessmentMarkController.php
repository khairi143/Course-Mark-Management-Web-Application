<?php
namespace App\Controllers;

use App\Models\StudentAssessmentMark;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentAssessmentMarkController {
    private $model;

    public function __construct() {
        $this->model = new StudentAssessmentMark();
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

    public function getByAssessment(Request $request, Response $response, $args): Response {
        $data = $this->model->getByAssessment($args['assessment_id']);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $success = $this->model->create($body['student_id'], $body['assessment_id'], $body['marks_obtained']);
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
