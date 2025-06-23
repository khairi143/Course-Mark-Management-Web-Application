<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;

class JwtMiddleware
{
    private string $secret;
    private array $unprotectedRoutes;

    public function __construct(string $secret, array $unprotectedRoutes = [])
    {
        $this->secret = $secret;
        $this->unprotectedRoutes = $unprotectedRoutes;
    }

    public function __invoke(Request $request, Handler $handler): Response
    {
        $path = $request->getUri()->getPath();

        foreach ($this->unprotectedRoutes as $route) {
            if (stripos($path, $route) !== false) {
                return $handler->handle($request);
            }
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            return $this->unauthorized("Missing token.");
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $request = $request->withAttribute('jwt', $decoded);
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->unauthorized("Invalid token: " . $e->getMessage());
        }
    }

    private function unauthorized(string $message): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}
