<!-- src/controllers/LecturerController.php -->
<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\Lecturer;

class LecturerController {

    // Get all lecturers
    public function getAll(Request $request, Response $response, $args) {
        $lecturers = Lecturer::getAll();
        $response->getBody()->write(json_encode($lecturers));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Get a specific lecturer by ID
    public function getById(Request $request, Response $response, $args) {
        $lecturer = Lecturer::getById($args['id']);
        if ($lecturer) {
            $response->getBody()->write(json_encode($lecturer));
            return $response->withHeader('Content-Type', 'application/json');
            //return $response->withJson($lecturer);
        }
        return $response->withStatus(404)->getBody()->write("Lecturer not found.");
    }

    // Create a new lecturer
    public function create(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);

        if (!isset($data['name'], $data['email'])) {
            return $response->withStatus(400)->getBody()->write('Invalid input');
        }

        $lecturer = Lecturer::create($data['name'], $data['email'], $data['course_id']);
        $response->getBody()->write(json_encode($lecturer));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        //return $response->withJson($lecturer, 201);
    }

    // Update an existing lecturer
    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody(), true);
        $updated = Lecturer::update($args['id'], $data);
        
        if ($updated) {
            $response->getBody()->write(json_encode($updated));
            return $response->withHeader('Content-Type', 'application/json');
            //return $response->withJson($updated);
        }

        return $response->withStatus(404)->getBody()->write("Lecturer not found.");
    }

    // Delete a lecturer
    public function delete(Request $request, Response $response, $args) {
        $deleted = Lecturer::delete($args['id']);
        
        if ($deleted) {
            return $response->withStatus(204);
        }

        return $response->withStatus(404)->getBody()->write("Lecturer not found.");
    }
}