<?php
use Slim\App;
use App\Controllers\FinalExamMarkController;

return function (App $app) {
    $app->get('/api/final-exam-marks', FinalExamMarkController::class . ':getAll');
    $app->get('/api/final-exam-marks/{id}', FinalExamMarkController::class . ':get');
    $app->get('/api/final-exam-marks/course/{course_id}', FinalExamMarkController::class . ':getByCourse');
    $app->post('/api/final-exam-marks', FinalExamMarkController::class . ':create');
    $app->put('/api/final-exam-marks/{id}', FinalExamMarkController::class . ':update');
    $app->delete('/api/final-exam-marks/{id}', FinalExamMarkController::class . ':delete');
};
