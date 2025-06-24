<?php

use App\Controllers\AdminController;
use Slim\App;

return function (App $app) {
    // User management
    $app->group('/api/users', function ($group) {
        $group->get('', [AdminController::class, 'getAllUsers']);
        $group->post('', [AdminController::class, 'createUser']);
        $group->put('/reset-password', [AdminController::class, 'resetPassword']);
        $group->put('/{id}', [AdminController::class, 'updateUser']);
        $group->delete('/{id}', [AdminController::class, 'deleteUser']);
    });

    // Enrollment management
    $app->group('/api/enrollments', function ($group) {
        $group->get('', [AdminController::class, 'getAllEnrollments']);
        $group->get('/{id}', [AdminController::class, 'getEnrollment']);
        $group->post('', [AdminController::class, 'createEnrollment']);
        $group->put('/{id}', [AdminController::class, 'updateEnrollment']);
        $group->delete('/{id}', [AdminController::class, 'deleteEnrollment']);
    });

    // Logs management
    $app->get('/api/logs', [AdminController::class, 'getLogs']);
    $app->post('/api/logs', [AdminController::class, 'createLog']);
    $app->put('/api/logs/{id}', [AdminController::class, 'markLogAsReviewed']);

    // Course management
    $app->post('/api/courses', [AdminController::class, 'createCourse']);
    $app->get('/api/courses', [AdminController::class, 'getAllCourse']);
    $app->put('/api/courses/{id}', [AdminController::class, 'updateCourse']);
    $app->delete('/api/courses/{id}', AdminController::class . ':deleteCourse');


    // Section management
    $app->group('/api/sections', function ($group) {
        $group->put('/{id}/lecturer', AdminController::class . ':updateLecturer');
        $group->get('/by-course/{course_id}', AdminController::class . ':getSectionsByCourse');
    });

    $app->group('/api/advisors', function ($group) {
        $group->get('', AdminController::class . ':getAllAdvisor');
        $group->get('/{id}', AdminController::class . ':getAdvisorById');
    });

}; 