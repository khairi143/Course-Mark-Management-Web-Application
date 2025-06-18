<?php
namespace App\Controllers;

use App\Models\Course;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseController
{
    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new Course();
    }

    // GET /api/courses
    public function getAll(Request $request, Response $response): Response
    {
        $courses = $this->courseModel->getAll();
        $response->getBody()->write(json_encode($courses));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateLecturer(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $lecturerId = $data['lecturer_id'] ?? null;

        if (!$lecturerId) {
            $response->getBody()->write(json_encode(['error' => 'Lecturer ID is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->courseModel->updateLecturer((int)$args['id'], (int)$lecturerId);

        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $success ? 'Updated successfully' : 'Failed to update'
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

}
