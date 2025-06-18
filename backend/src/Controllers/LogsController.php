<?php

namespace App\Controllers;

use App\Models\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogsController
{
    private $logs;

    public function __construct()
    {
        $this->logs = new Log();
    }

    // GET /api/logs
    public function getLogs(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $logs = $this->logs->getAll($params);

        $response->getBody()->write(json_encode($logs));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/logs
    public function createLog(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $userId = $data['user_id'] ?? null;
        $action = $data['action'] ?? '';
        $details = $data['details'] ?? '';

        if (!$action) {
            $response->getBody()->write(json_encode(['error' => 'Action is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $this->logs->create($userId, $action, $details);
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/logs/{id}/review
    public function markAsReviewed(Request $request, Response $response, array $args): Response
    {
        $logId = $args['id'];
        $this->logs->markReviewed($logId);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
