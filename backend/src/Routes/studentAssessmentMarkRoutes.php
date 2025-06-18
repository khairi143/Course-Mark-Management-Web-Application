<?php
use App\Controllers\StudentAssessmentMarkController;
use Slim\App;

return function (App $app) {
    $app->group('/api/student-assessment-marks', function ($group) {
        $group->get('', StudentAssessmentMarkController::class . ':getAll');
        $group->get('/{id}', StudentAssessmentMarkController::class . ':get');
        $group->get('/assessment/{assessment_id}', StudentAssessmentMarkController::class . ':getByAssessment');
        $group->post('', StudentAssessmentMarkController::class . ':create');
        $group->put('/{id}', StudentAssessmentMarkController::class . ':update');
        $group->delete('/{id}', StudentAssessmentMarkController::class . ':delete');
    });
};
