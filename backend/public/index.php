<?php
//php -S localhost:8000 -t public
//npm run serve
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy; // Make sure this is imported

use App\db;
use App\Middleware\JwtMiddleware;
use App\Controllers\AuthController;
use App\Controllers\StudentController;
use App\Services\StudentService;

$app = AppFactory::create();
$secretKey = "my-secret-key"; // Keep this secure in production (environment variable)
$unprotectedRoutes = ['/api/register', '/api/login'];
$jwtMiddleware = new JwtMiddleware($secretKey, $unprotectedRoutes);
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

(require __DIR__ . '/../src/Controllers/LecturerController.php')($app, $jwtMiddleware); // link to controller for lecturer routes
$app->post('/api/register', function (Request $request, Response $response) use ($secretKey) {
    try {
        $registrationData = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($registrationData)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided. Please ensure your request body is valid JSON.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $database = new db();
        $studentService = new StudentService();
        $authController = new AuthController($database, $studentService, $secretKey);

        $result = $authController->register($registrationData); // Delegate to AuthController

        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

    } catch (\InvalidArgumentException $e) { // Use \ for global exceptions if not using `use` statement
        $errorBody = json_encode(['error' => $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    } catch (\RuntimeException $e) { // Use \ for global exceptions
        $statusCode = 500;
        if ($e->getCode() === 409) {
            $statusCode = 409;
        }
        $errorBody = json_encode(['error' => $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $errorBody = json_encode(['error' => 'An unexpected server error occurred: ' . $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// POST /api/login - Handles user authentication and JWT token generation
$app->post('/api/login', function (Request $request, Response $response) use ($secretKey) {
    try {
        $credentials = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($credentials)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided for login.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $database = new db();
        $studentService = new StudentService();
        $authController = new AuthController($database, $studentService, $secretKey);

        $result = $authController->login($credentials); // Delegate to AuthController

        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    } catch (\InvalidArgumentException $e) { // Use \ for global exceptions
        $errorBody = json_encode(['error' => $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    } catch (\RuntimeException $e) { // Use \ for global exceptions
        $statusCode = $e->getCode() ?: 500;
        $errorBody = json_encode(['error' => $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $errorBody = json_encode(['error' => 'An unexpected server error occurred during login: ' . $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});


// Group for protected API routes, applying JwtMiddleware to all routes within this group
// The group's prefix is '/api'. Routes inside should NOT repeat '/api'.
$app->group('/api', function (RouteCollectorProxy $group) use ($secretKey){

    // NEW: Logout Route - Protected (User must be logged in to "logout" from backend)
    // Path becomes /api/logout (because of group prefix)
    $group->post('/logout', function (Request $request, Response $response) use ($secretKey) { // Added use($secretKey) for AuthController
        $database = new db();
        $studentService = new StudentService();
        $authController = new AuthController($database, $studentService, $secretKey); // Pass secretKey here

        try {
            $result = $authController->logout();

            $response->getBody()->write(json_encode($result));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $errorBody = json_encode(['error' => 'An error occurred during logout: ' . $e->getMessage()]);
            $response->getBody()->write($errorBody);
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // GET /api/me/role - Example protected route to get user role from JWT
    // Path becomes /api/me/role
    $group->get('/me/role', function (Request $request, Response $response) {
        $jwt = $request->getAttribute('jwt');
        $role = $jwt->data->role ?? null;
        $response->getBody()->write(json_encode(['role' => $role]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    // GET /api/students - Get all student profiles (requires authentication)
    // Path becomes /api/students
    $group->get('/students', function (Request $request, Response $response) {
        try {
            $database = new db();
            $controller = new StudentController($database);
            $students = $controller->getAll();

            $response->getBody()->write(json_encode($students));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\RuntimeException $e) {
            $errorBody = json_encode(['error' => 'Failed to retrieve students: ' . $e->getMessage()]);
            $response->getBody()->write($errorBody);
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $errorBody = json_encode(['error' => 'An unexpected server error occurred: ' . $e->getMessage()]);
            $response->getBody()->write($errorBody);
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // GET /api/students/{id} - Get a specific student profile by ID (requires authentication)
    // Path becomes /api/students/{id}
    $group->get('/students/{id}', function (Request $request, Response $response, array $args) {
        try {
            $id = (int)$args['id'];

            $database = new db();
            $controller = new StudentController($database);
            $student = $controller->getStudentById($id);

            if ($student === false) {
                $errorBody = json_encode(['error' => 'Student not found.']);
                $response->getBody()->write($errorBody);
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode($student));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\RuntimeException $e) {
            $errorBody = json_encode(['error' => 'Failed to retrieve student: ' . $e->getMessage()]);
            $response->getBody()->write($errorBody);
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $errorBody = json_encode(['error' => 'An unexpected server error occurred: ' . $e->getMessage()]);
            $response->getBody()->write($errorBody);
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

})->add($jwtMiddleware);

$app->add(function (Request $request, RequestHandler $handler): Response {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*') // Change to specific frontend domain(s) in production!
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->run();
