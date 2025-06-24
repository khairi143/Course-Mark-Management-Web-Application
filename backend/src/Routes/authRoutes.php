<?php

use App\Controllers\AuthController;
use Slim\App;
 
return function (App $app) {
    $app->post('/api/auth/login-staff', [AuthController::class, 'loginStaff']);
    $app->post('/api/auth/login-student', [AuthController::class, 'loginStudent']);
}; 