<?php

use App\Controllers\AssessmentController;
use Slim\App;

return function (App $app) {
    $app->group('/api/assessments', function ($group) {
        $group->get('', AssessmentController::class . ':getAll');
        $group->get('/course/{course_id}', AssessmentController::class . ':getByCourse');
        $group->get('/{id}', AssessmentController::class . ':get');
        $group->post('', AssessmentController::class . ':create');
        $group->put('/{id}', AssessmentController::class . ':update');
        $group->delete('/{id}', AssessmentController::class . ':delete');
    });
};
