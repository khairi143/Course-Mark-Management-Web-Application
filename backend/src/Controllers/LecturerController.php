<?php
namespace App\Controllers;

use App\Models\Lecturer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LecturerController
{
    protected $lecturerModel;

    public function __construct()
    {
        $this->lecturerModel = new Lecturer();
    }

    // GET /api/lecturers
    public function getAll(Request $request, Response $response): Response
    {
        $lecturers = $this->lecturerModel->getAll();
        $response->getBody()->write(json_encode($lecturers));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
