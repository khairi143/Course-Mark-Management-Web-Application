<!-- src/controllers/AssessmentComponentController.php -->
<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\AssessmentComponent;

class AssessmentComponentController {

    // Get all components for a specific course
    public function getAll(Request $request, Response $response, $args) {
        $courseId = $request->getQueryParams()['course_id'] ?? null;
        if (!$courseId) {
            return $response->withStatus(400)->getBody()->write("Course ID is required.");
        }
        
        $components = AssessmentComponent::getAll($courseId);
        $response->getBody()->write(json_encode($components));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Get a specific assessment component by ID
    public function getById(Request $request, Response $response, $args) {
        $component = AssessmentComponent::getById($args['id']);
        if ($component) {
            $response->getBody()->write(json_encode($component));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404)->getBody()->write("Component not found.");
    }

    // Create a new assessment component
    public function create(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);

        if (!isset($data['course_id'], $data['name'], $data['weight'], $data['max_mark'])) {
            return $response->withStatus(400)->getBody()->write('Invalid input');
        }

        $component = AssessmentComponent::create($data['course_id'], $data['name'], $data['weight'], $data['max_mark']);
        $response->getBody()->write(json_encode($component));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // Update an assessment component
    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);
        $updated = AssessmentComponent::update($args['id'], $data);
        
        if ($updated) {
            $response->getBody()->write(json_encode($updated));
            return $response->withHeader('Content-Type', 'application/json');
            //return $response->withJson($updated);
        }

        return $response->withStatus(404)->getBody()->write("Component not found.");
    }

    // Delete an assessment component
    public function delete(Request $request, Response $response, $args) {
        $deleted = AssessmentComponent::delete($args['id']);
        
        if ($deleted) {
            return $response->withStatus(204);
        }

        return $response->withStatus(404)->getBody()->write("Component not found.");
    }
}
