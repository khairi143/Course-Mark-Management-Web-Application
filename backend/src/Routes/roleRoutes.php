<?php

use Slim\App;
use App\Controllers\RoleController;

return function (App $app) {
    $app->get('/api/roles', RoleController::class . ':getAll');
};
