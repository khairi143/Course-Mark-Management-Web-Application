<!-- src/controllers/AssessmentMarkController.php -->
<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\AssessmentMark;

class AssessmentMarkController {

    // Get all marks for a specific student
    public function getAll(Request $request, Response $response, $args) {
        $enrollmentId = $request->getQueryParams()['enrollment_id'] ?? null;
        if (!$enrollmentId) {
            return $response->withStatus(400)->getBody()->write("Enrollment ID is required.");
        }
        
        $marks = AssessmentMark::getAll($enrollmentId);
        $response->getBody()->write(json_encode($marks));
        return $response->withHeader('Content-Type', 'application/json');
        //return $response->withJson($marks);
    }

    // Get a specific mark by ID
    public function getById(Request $request, Response $response, $args) {
        $mark = AssessmentMark::getById($args['id']);
        if ($mark) {
            $response->getBody()->write(json_encode($mark));
            return $response->withHeader('Content-Type', 'application/json');
            //return $response->withJson($mark);
        }
        return $response->withStatus(404)->getBody()->write("Mark not found.");
    }

    // Create a new assessment mark
    public function create(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);

        if (!isset($data['enrollment_id'], $data['component_id'], $data['mark_obtained'])) {
            return $response->withStatus(400)->getBody()->write('Invalid input');
        }

        $mark = AssessmentMark::create($data['enrollment_id'], $data['component_id'], $data['mark_obtained']);
        $response->getBody()->write(json_encode($mark));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        //return $response->withJson($mark, 201);
    }

    // Update an assessment mark
    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);
        $updated = AssessmentMark::update($args['id'], $data);
        
        if ($updated) {
            $response->getBody()->write(json_encode($updated));
            return $response->withHeader('Content-Type', 'application/json');
            //return $response->withJson($updated);
        }

        return $response->withStatus(404)->getBody()->write("Mark not found.");
    }

    // Delete an assessment mark
    public function delete(Request $request, Response $response, $args) {
        $deleted = AssessmentMark::delete($args['id']);
        
        if ($deleted) {
            return $response->withStatus(204);
        }

        return $response->withStatus(404)->getBody()->write("Mark not found.");
    }
}
