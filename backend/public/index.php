<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/jwtMiddleware.php';


use Slim\Factory\AppFactory; 
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

$secretKey = "my-secret-key";

$app = AppFactory::create();
$jwtMiddleware = new JwtMiddleware($secretKey);

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

//publicly accessible route
//login
$app->post('/login', function (Request $request, Response $response) use ($secretKey) {
    $data = json_decode($request->getBody()->getContents(), true);

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
        $response->getBody()->write(json_encode(['error' => 'Username and password required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // if ($user && password_verify($password, $user['password'])) {
    if ($user && $password === $user['password']) {

        $issuedAt = time();
        $expire = $issuedAt + 3600;

        $payload = [
            'user' => $user['username'],
            'role' => $user['role'],
            'iat' => $issuedAt,
            'exp' => $expire
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
    return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
});

$app->get('/me/role', function (Request $request, Response $response) {
    $jwt = $request->getAttribute('jwt');
    if (!$jwt) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    $role = $jwt->role ?? null;
    $response->getBody()->write(json_encode(['role' => $role]));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

// // Lecturer CRUD routes
// $app->group('/lecturers', function () use ($app) {
//     // Get all lecturers
//     $app->get('/getAll', \App\Controllers\LecturerController::class . ':getAll');

//     // Get a specific lecturer by ID
//     $app->get('/get/{id}', \App\Controllers\LecturerController::class . ':getById');

//     // Create a new lecturer
//     $app->post('/create', \App\Controllers\LecturerController::class . ':create');

//     // Update an existing lecturer
//     $app->put('/update/{id}', \App\Controllers\LecturerController::class . ':update');

//     // Delete a lecturer
//     $app->delete('/delete/{id}', \App\Controllers\LecturerController::class . ':delete');

//     // Assessment Components CRUD routes
//     $app->group('/assessment-components', function () use ($app) {
//         // Get all assessment components for a course
//         $app->get('/getAll', \App\Controllers\AssessmentComponentController::class . ':getAll');

//         // Get a specific assessment component by ID
//         $app->get('/get/{id}', \App\Controllers\AssessmentComponentController::class . ':getById');

//         // Create a new assessment component
//         $app->post('/create', \App\Controllers\AssessmentComponentController::class . ':create');

//         // Update an existing assessment component
//         $app->put('/update/{id}', \App\Controllers\AssessmentComponentController::class . ':update');

//         // Delete an assessment component
//         $app->delete('/delete/{id}', \App\Controllers\AssessmentComponentController::class . ':delete');
//     });

//     // Assessment Marks CRUD routes
//     $app->group('/assessment-marks', function () use ($app) {
//         // Get all marks for a student
//         $app->get('/getAll', \App\Controllers\AssessmentMarkController::class . ':getAll');

//         // Get specific mark for a student in an assessment component
//         $app->get('/get/{id}', \App\Controllers\AssessmentMarkController::class . ':getById');

//         // Create a mark for a student
//         $app->post('/create', \App\Controllers\AssessmentMarkController::class . ':create');

//         // Update an existing mark for a student
//         $app->put('/update/{id}', \App\Controllers\AssessmentMarkController::class . ':update');

//         // Delete a mark for a student
//         $app->delete('/delete/{id}', \App\Controllers\AssessmentMarkController::class . ':delete');
//     });

// })->add($jwtMiddleware);

// GET ALL /product – accessible to all registered users
$app->get('/product', function ($request, $response) {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM PRODUCT");
    $products = $stmt->fetchAll();

    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

// Lecturers Modules CRUD routes:
// GET ALL /students
$app->get('/students', function ($request, $response) {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM STUDENTS");
    $products = $stmt->fetchAll();

    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

//GET 1 PRODUCT - accessible to all regs - normal user and admin
$app->get('/product/{id}', function ($request, $response, $args) {
    $id = $args['id'];

    if (!is_numeric($id)) {
        $response->getBody()->write(json_encode(['error' => 'Invalid product ID']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        $response->getBody()->write(json_encode(['error' => 'Product not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($product));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

// POST /product – for admin only
$app->post('/product', function ($request, $response) use ($secretKey) {
    $jwt = $request->getAttribute('jwt');

    if (($jwt->role ?? '') !== 'admin') {
        $error = ['error' => 'Access denied: admin only'];
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    $data = json_decode($request->getBody()->getContents(), true);

    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO PRODUCT (name, price, image) VALUES (?, ?, ?)");
    $stmt->execute([
        $data['name'] ?? null,
        $data['price'] ?? null,
        $data['image'] ?? null
    ]);

    $response->getBody()->write(json_encode(['message' => 'Product added']));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new JwtMiddleware($secretKey));

// CORS preflight support
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->run();
