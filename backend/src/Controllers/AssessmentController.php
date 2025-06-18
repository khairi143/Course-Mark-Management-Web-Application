<?php
namespace App\Controllers;

use App\Models\Assessment;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AssessmentController {
    private $model;

    public function __construct() {
        $this->model = new Assessment();
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
        $data = $this->model->getByCourseId($args['course_id']);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $success = $this->model->create(
            $body['course_id'],
            $body['assessment_name'],
            $body['max_marks'],
            $body['weight_percentage']
        );
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args): Response {
        $body = $request->getParsedBody();
        $success = $this->model->update(
            $args['id'],
            $body['assessment_name'],
            $body['max_marks'],
            $body['weight_percentage']
        );
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args): Response {
        $success = $this->model->delete($args['id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
