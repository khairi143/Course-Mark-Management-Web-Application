<?php

use Slim\App;
use App\Controllers\CourseController;

return function (App $app) {
    $app->get('/api/courses', CourseController::class . ':getAll');
    $app->put('/api/courses/{id}', CourseController::class . ':updateLecturer');
};
