<?php

use App\Controllers\EnrollmentController;
use Slim\App;

return function (App $app) {
    $app->group('/api/enrollments', function ($group) {
        $group->get('', EnrollmentController::class . ':getAll');
        $group->get('/{id}', EnrollmentController::class . ':get');
        $group->post('', EnrollmentController::class . ':create');
        $group->put('/{id}', EnrollmentController::class . ':update');
        $group->delete('/{id}', EnrollmentController::class . ':delete');
    });
};
