<?php

use Slim\App;
use App\Controllers\LecturerController;

return function (App $app) {
    $app->get('/api/lecturers', LecturerController::class . ':getAll');
    $app->get('/api/lecturers/{id}/courses-with-sections', LecturerController::class . ':getCoursesWithSections');
    $app->get('/api/lecturers/by-user/{user_id}', LecturerController::class . ':getLecturerIdByUserId');
    $app->get('/api/lecturers/students', LecturerController::class . ':getAllStudents');
    $app->get('/api/lecturers/students/{id}', LecturerController::class . ':getStudentById');
    $app->post('/api/lecturers/students', LecturerController::class . ':createStudent');
    $app->put('/api/lecturers/students/{id}', LecturerController::class . ':updateStudent');
    $app->delete('/api/lecturers/students/{id}', LecturerController::class . ':deleteStudent');
    $app->get('/api/lecturers/enrollments', LecturerController::class . ':getAllEnrollments');
    $app->get('/api/lecturers/enrollments/{id}', LecturerController::class . ':getEnrollmentById');
    $app->post('/api/lecturers/enrollments', LecturerController::class . ':createEnrollment');
    $app->put('/api/lecturers/enrollments/{id}', LecturerController::class . ':updateEnrollment');
    $app->delete('/api/lecturers/enrollments/by-student-section', LecturerController::class . ':removeEnrollmentByStudentAndSection');
    $app->delete('/api/lecturers/enrollments/{id}', LecturerController::class . ':deleteEnrollment');
    $app->get('/api/lecturers/sections/{section_id}/students', LecturerController::class . ':getStudentsBySectionId');
    $app->get('/api/lecturers/assessments', LecturerController::class . ':getAllAssessments');
    $app->get('/api/lecturers/assessments/{id}', LecturerController::class . ':getAssessmentById');
    $app->get('/api/lecturers/sections/{section_id}/assessments', LecturerController::class . ':getAssessmentsBySectionId');
    $app->post('/api/lecturers/assessments', LecturerController::class . ':createAssessment');
    $app->put('/api/lecturers/assessments/{id}', LecturerController::class . ':updateAssessment');
    $app->delete('/api/lecturers/assessments/{id}', LecturerController::class . ':deleteAssessment');
    $app->post('/api/lecturers/sections/{section_id}/students/{student_id}/assessment-marks', LecturerController::class . ':saveStudentAssessmentMarks');
    $app->get('/api/lecturers/sections/{section_id}/students/{student_id}/assessment-marks', LecturerController::class . ':getStudentAssessmentMarks');
    $app->post('/api/lecturers/sections/{section_id}/students/{student_id}/enrollment-total-percentage', LecturerController::class . ':saveOrUpdateEnrollmentTotalPercentage');
    $app->get('/api/lecturers/assessment-marks-join', LecturerController::class . ':getAssessmentMarksJoin');
    $app->get('/api/lecturers/total-percentage', LecturerController::class . ':getTotalPercentageByStudentIdAndSectionId');
    $app->get('/api/lecturers/remarks/{section_id}', LecturerController::class . ':getRemarksBySection');

};
