<?php

use Slim\App;
use App\Controllers\LecturerController;

return function (App $app) {
    $app->get('/api/lecturers', LecturerController::class . ':getAll');
};
