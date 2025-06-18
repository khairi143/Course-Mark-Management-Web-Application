<?php
namespace App\Controllers;

use App\Models\Enrollment;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EnrollmentController {
    private $enrollmentModel;

    public function __construct() {
        $this->enrollmentModel = new Enrollment();
    }

    private function respondWithJson(Response $response, $data, int $status = 200): Response {
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function getAll(Request $request, Response $response, array $args): Response {
        $data = $this->enrollmentModel->getAll();
        return $this->respondWithJson($response, $data);
    }

    public function get(Request $request, Response $response, array $args): Response {
        $data = $this->enrollmentModel->getById($args['id']);
        return $this->respondWithJson($response, $data);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $body = $request->getParsedBody();
        $success = $this->enrollmentModel->create($body['student_id'], $body['course_id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }

    public function update(Request $request, Response $response, array $args): Response {
        $body = $request->getParsedBody();
        $success = $this->enrollmentModel->update($args['id'], $body['student_id'], $body['course_id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $success = $this->enrollmentModel->delete($args['id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }
}
