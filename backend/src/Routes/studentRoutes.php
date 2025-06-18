<?php

use Slim\App;
use App\Controllers\StudentController;

return function (App $app) {
    $app->group('/api/students', function ($group) {
        $group->get('', StudentController::class . ':getAll');
        $group->post('', StudentController::class . ':create');
        $group->put('/{id}', StudentController::class . ':update');
        $group->delete('/{id}', StudentController::class . ':delete');
    });
};
