<?php

use Slim\App;
use App\Controllers\AdvisorController;

return function (App $app) {
    $app->group('/api/advisor', function ($group) {
        $group->get('/{user_id}/students', AdvisorController::class . ':getAdvisees');
        $group->get('/{user_id}/advisees-marks', AdvisorController::class . ':getAdviseesMarksByUserId');
        $group->get('/notes/{userId}/{studentId}', [AdvisorController::class, 'getNotes']);
        $group->post('/notes/{userId}', [AdvisorController::class, 'createNote']);
        $group->put('/notes/{userId}/{noteId}', [AdvisorController::class, 'updateNote']);
        $group->delete('/notes/{userId}/{noteId}', [AdvisorController::class, 'deleteNote']);
        $group->get('/{userId}/students/{studentId}/export', AdvisorController::class . ':exportConsultationReport');
    });
};
