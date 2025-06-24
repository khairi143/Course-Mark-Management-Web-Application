<?php

namespace App\Controllers;

use App\Models\Advisor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdvisorController
{

    private $advisorModel;

    public function __construct()
    {
        $this->advisorModel = new Advisor();
    }

    private function createJsonBody(array $data)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, json_encode($data));
        rewind($stream);
        return new \Slim\Psr7\Stream($stream);
    }


    public function getAdvisees($request, $response, $args) {
        $userId = $args['user_id'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $students = $this->advisorModel->getAdviseesById($userId);

            $response->getBody()->write(json_encode($students));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to fetch advisees']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getByUserId(Request $request, Response $response, $args): Response
    {
        $advisor = $this->advisorModel->getByUserId($args['user_id']);
        if ($advisor) {
            $response->getBody()->write(json_encode($advisor));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Advisor not found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAdviseesMarksByUserId($request, $response, $args) {
        $data = $this->advisorModel->getAdviseesMarksByUserId($args['user_id']);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getNotes($request, $response, $args)
    {
        $userId = $args['userId'];
        $studentId = $args['studentId'];

        $advisorModel = new Advisor();
        $advisor = $advisorModel->getIdByUserId($userId);
        if (!$advisor) {
            $response->getBody()->write(json_encode(['error' => 'Advisor not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $notes = $advisorModel->getAdvisorNotesByStudentId($studentId, $advisor['id']);
        $response->getBody()->write(json_encode($notes));
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function createNote($request, $response, $args)
    {
        $userId = $args['userId'];
        $body = $request->getParsedBody();

        $advisorModel = new Advisor();
        $advisor = $advisorModel->getIdByUserId($userId);
        if (!$advisor) {
            $response->getBody()->write(json_encode(['error' => 'Advisor not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $studentId = $body['student_id'] ?? null;
        $title = $body['title'] ?? '';
        $content = $body['content'] ?? '';
        $date = $body['date'] ?? date('Y-m-d');

        if (!$studentId) {
            $response->getBody()->write(json_encode(['error' => 'Student ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $advisorModel->addAdvisorNote($advisor['id'], $studentId, $title, $content, $date);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }


    // Update note
public function updateNote($request, $response, $args)
{
    $userId = $args['userId'];
    $noteId = $args['noteId'];
    $body = $request->getParsedBody();

    $advisorModel = new Advisor();
    $advisor = $advisorModel->getIdByUserId($userId);
    if (!$advisor) {
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createJsonBody(['error' => 'Advisor not found']));
    }

    $title = $body['title'] ?? '';
    $content = $body['content'] ?? '';
    $date = $body['date'] ?? date('Y-m-d');

    $updated = $advisorModel->updateAdvisorNote($noteId, $advisor['id'], $title, $content, $date);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withBody($this->createJsonBody(['success' => $updated]));
}

// Delete note
public function deleteNote($request, $response, $args)
{
    $userId = $args['userId'];
    $noteId = $args['noteId'];

    $advisorModel = new Advisor();
    $advisor = $advisorModel->getIdByUserId($userId);
    if (!$advisor) {
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->createJsonBody(['error' => 'Advisor not found']));
    }

    $deleted = $advisorModel->deleteAdvisorNote($noteId, $advisor['id']);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withBody($this->createJsonBody(['success' => $deleted]));
}

public function exportConsultationReport($request, $response, $args)
{
    $userId = $args['userId'];
    $studentId = $args['studentId'];
    $format = $request->getQueryParams()['format'] ?? 'csv';

    $advisorModel = new Advisor();
    $advisor = $advisorModel->getIdByUserId($userId);

    if (!$advisor) {
        return $response->withStatus(404)->withJson(['error' => 'Advisor not found']);
    }

    $notes = $advisorModel->getAdvisorNotesByStudentId($studentId, $advisor['id']);

    if ($format === 'csv') {
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Title', 'Content', 'Date']);
        foreach ($notes as $note) {
            fputcsv($csv, [$note['title'], $note['content'], $note['date']]);
        }
        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        $response->getBody()->write($csvContent);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="consultation_report.csv"');
    }

    // Optional: Add PDF generation if needed
    return $response->withJson(['error' => 'Format not supported'], 400);
}




}