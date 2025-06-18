<?php

use Slim\App;
use App\Controllers\LogsController;

return function (App $app) {
    $app->get('/api/logs', [LogsController::class, 'getLogs']);
    $app->post('/api/logs', [LogsController::class, 'createLog']);
    $app->put('/api/logs/{id}/review', [LogsController::class, 'markAsReviewed']);
};
