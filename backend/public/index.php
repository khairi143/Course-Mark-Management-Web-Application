    <?php
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
    use Slim\Routing\RouteCollectorProxy;

    use App\db;
    use App\Middleware\JwtMiddleware;
    use App\Controllers\AuthController;
    use App\Controllers\StudentController;
    use App\Services\StudentService;

    $app = AppFactory::create();
    $secretKey = "my-secret-key";
    $unprotectedRoutes = ['/api/register', '/api/login'];
    $jwtMiddleware = new JwtMiddleware($secretKey, $unprotectedRoutes);
    $app->addRoutingMiddleware();
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    // Register route
    $app->post('/api/register', function (Request $request, Response $response) use ($secretKey) {
    try {
        $registrationData = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($registrationData)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided. Please ensure your request body is valid JSON.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $database = new db();
        $studentService = new StudentService($database);
        $authController = new AuthController($database, $studentService(), $secretKey);

        $result = $authController->register($registrationData); // Delegate to AuthController

        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

    } catch (\InvalidArgumentException $e) { // Use \ for global exceptions if not using use statement
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


    // Login route
    $app->post('/api/login', function (Request $request, Response $response) use ($secretKey) {
    try {
        $credentials = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($credentials)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided for login.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $database = new db();
        $studentService = new StudentService($database);
        $authController = new AuthController($database, $studentService(), $secretKey);

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


    $app->group('/api', function (RouteCollectorProxy $group) use ($secretKey) {
        $group->get('/me/role', function (Request $request, Response $response) {
            $jwt = $request->getAttribute('jwt');
            $role = $jwt->data->role ?? null;
            $response->getBody()->write(json_encode(['role' => $role]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        });
    });

    $app->add(function (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    });

    $app->run();
