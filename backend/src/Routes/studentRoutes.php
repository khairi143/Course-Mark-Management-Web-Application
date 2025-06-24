<?php

use Slim\App;
use App\Controllers\StudentController;

return function (App $app) {
    $app->group('/api/students', function ($group) {
        $group->get('', StudentController::class . ':getAll');
        $group->post('', StudentController::class . ':create');
        $group->put('/{id}', StudentController::class . ':update');
        $group->delete('/{id}', StudentController::class . ':delete');
        $group->get('/{id}/courses', StudentController::class . ':getCourses'); // ✅ New route
        $group->get('/by-user/{userId}', StudentController::class . ':getIdByUserId');
        $group->get('/{student_id}/sections/{section_id}/marks', StudentController::class . ':getAssessmentMarksByStudentAndSection');
        $group->get('/{student_id}/total-percentage/section/{section_id}', StudentController::class . ':getTotalPercentageByStudentIdAndSectionId');
        $group->get('/assessment-marks/section/{section_id}', StudentController::class . ':getStudentsAssessmentMarksBySection');
        $group->get('/section/{section_id}/percentages', StudentController::class . ':getTotalPercentageBySectionId');
        $group->post('/remarks', StudentController::class . ':submitRemarkRequest');
        // Notifications
        $group->get('/{user_id}/notifications', StudentController::class . ':getNotifications');
        $group->get('/{user_id}/notifications/unread-count', StudentController::class . ':getUnreadNotificationCount');
        $group->put('/{user_id}/notifications/{notification_id}/read', StudentController::class . ':markNotificationAsRead');
        $group->put('/{user_id}/notifications/mark-all', StudentController::class . ':markAllNotificationsAsRead');
        $group->delete('/{user_id}/notifications/{notification_id}', StudentController::class . ':deleteNotification');

        

    });
};
