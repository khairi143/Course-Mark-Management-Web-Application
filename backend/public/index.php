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

(require __DIR__ . '/../src/Routes/lecturerRoutes.php')($app);
(require __DIR__ . '/../src/Routes/studentRoutes.php')($app);
(require __DIR__ . '/../src/Routes/authRoutes.php')($app);
(require __DIR__ . '/../src/Routes/adminRoutes.php')($app);
(require __DIR__ . '/../src/Routes/advisorRoutes.php')($app);
(require __DIR__ . '/../src/Routes/roleRoutes.php')($app);


$app->run();
