<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../src/middleware/jwtMiddleware.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Slim\Middleware\BodyParsingMiddleware;

$secretKey = "my-secret-key";

$app = AppFactory::create();
$jwtMiddleware = new JwtMiddleware($secretKey);
$app->addBodyParsingMiddleware();

(require __DIR__ . '/../src/Routes/userRoutes.php')($app);
(require __DIR__ . '/../src/Routes/roleRoutes.php')($app);
(require __DIR__ . '/../src/Routes/lecturerRoutes.php')($app);
(require __DIR__ . '/../src/Routes/courseRoutes.php')($app);
(require __DIR__ . '/../src/Routes/logRoutes.php')($app);
(require __DIR__ . '/../src/Routes/studentRoutes.php')($app);
(require __DIR__ . '/../src/Routes/enrollmentRoutes.php')($app);
(require __DIR__ . '/../src/Routes/assessmentRoutes.php')($app);
(require __DIR__ . '/../src/Routes/studentAssessmentMarkRoutes.php')($app);
(require __DIR__ . '/../src/Routes/finalExamMarkRoutes.php')($app);
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

// GET ALL /product – accessible to all registered users
$app->get('/product', function ($request, $response) {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM PRODUCT");
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


$app->run();
