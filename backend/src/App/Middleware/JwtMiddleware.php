<?php

declare(strict_types=1);

namespace App\Middleware; 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;       // <--- CRUCIAL: Implement this interface
use Psr\Http\Server\RequestHandlerInterface as RequestHandler; // Correct type hint for handler
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;      // Specific JWT exceptions
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;  // For 'nbf' (not before) claim
use InvalidArgumentException;            // For issues with payload structure etc.

class JwtMiddleware implements MiddlewareInterface // <--- THIS LINE IS THE FIX
{
    private string $secretKey;
    private array $unprotectedPaths; // Paths that do NOT require JWT

    public function __construct(string $secretKey, array $unprotectedPaths = [])
    {
        $this->secretKey = $secretKey;
        $this->unprotectedPaths = $unprotectedPaths;
    }

    /**
     * Process an incoming server request and return a response.
     * This method is required by Psr\Http\Server\MiddlewareInterface.
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response // <--- Use process method
    {
        // 1. Check if the current route is unprotected
        $path = $request->getUri()->getPath();

        foreach ($this->unprotectedPaths as $unprotectedPath) {
            // Simple check: if the path exactly matches or starts with an unprotected path
            // e.g., '/api/login' or '/api/register'
            if ($path === $unprotectedPath || str_starts_with($path, $unprotectedPath . '/')) {
                return $handler->handle($request); // Allow to proceed without JWT validation
            }
        }

        // 2. Attempt to get and validate JWT for protected routes
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            // No Authorization header, or empty
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization header missing.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Check for 'Bearer ' prefix and extract the token
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches) || empty($matches[1])) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid Authorization header format. Must be "Bearer <token>".']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = $matches[1];

        try {
            // Decode and verify the JWT
            // Using Key for php-jwt v6.0+
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256')); // Corrected property name $this->secretKey

            // Add the decoded JWT payload to the request attributes for downstream use
            $request = $request->withAttribute('jwt', $decoded);

            // Continue to the next middleware or route handler in the stack
            return $handler->handle($request);

        } catch (ExpiredException $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token has expired.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (SignatureInvalidException $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token signature.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (BeforeValidException $e) {
             $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token is not yet valid.']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (InvalidArgumentException $e) {
            // This might catch errors related to malformed JWTs (e.g., missing segments)
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Malformed token or invalid claims: ' . $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions during token processing
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized: ' . $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }

    // Removed the unused `unauthorizedResponse` method.
}
