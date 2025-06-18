<?php

use App\Controllers\UserController;
use Slim\App;

return function (App $app) {
    $app->group('/api/users', function ($group) {
        $group->get('', UserController::class . ':getAll');
        $group->post('', UserController::class . ':create');
        $group->put('/reset-password', UserController::class . ':resetPassword');
        $group->put('/{id}', UserController::class . ':update');
        $group->delete('/{id}', UserController::class . ':delete');
    });
};