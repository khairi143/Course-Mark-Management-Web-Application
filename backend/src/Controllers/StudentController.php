<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Notification;

class StudentController
{
    private $studentModel;
    private $courseModel;
    private $notificationModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->courseModel = new Course();
        $this->notificationModel = new Notification();
    }

    public function getAll(Request $request, Response $response, $args)
    {
        $students = $this->studentModel->getAll();
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $result = $this->studentModel->create($data);
        $response->getBody()->write(json_encode(['message' => 'Student added']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $this->studentModel->update($id, $data);
        $response->getBody()->write(json_encode(['message' => 'Student updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $this->studentModel->delete($id);
        $response->getBody()->write(json_encode(['message' => 'Student deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getCourses(Request $request, Response $response, $args)
    {
        $student_id = $args['id'];
        $courses = $this->courseModel->getCoursesByStudentId($student_id);
        $response->getBody()->write(json_encode($courses));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getIdByUserId(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $student = $this->studentModel->getIdByUserId($userId);

        if ($student) {
            $response->getBody()->write(json_encode($student));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Student not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }

    public function getAssessmentMarksByStudentAndSection($request, $response, $args)
    {
        $studentId = $args['student_id'];
        $sectionId = $args['section_id'];

        $markModel = new \App\Models\StudentAssessmentMark();
        $marks = $markModel->getAssessmentMarksByStudentAndSection($studentId, $sectionId);

        $response->getBody()->write(json_encode($marks));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getTotalPercentageByStudentIdAndSectionId(Request $request, Response $response, $args)
    {
        $studentId = $args['student_id'];
        $sectionId = $args['section_id'];

        $enrollmentModel = new \App\Models\Enrollment();
        $percentage = $enrollmentModel->getTotalPercentageByStudentIdAndSectionId($studentId, $sectionId);

        $response->getBody()->write(json_encode(['total_percentage' => $percentage]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getStudentsAssessmentMarksBySection($request, $response, $args)
    {
        $sectionId = $args['section_id'] ?? null;

        if (!$sectionId) {
            $response->getBody()->write(json_encode(['error' => 'Section ID is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $model = new \App\Models\StudentAssessmentMark();
            $data = $model->getStudentsAssessmentMarksBySection($sectionId);
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to fetch marks: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getTotalPercentageBySectionId($request, $response, $args)
    {
        $sectionId = $args['section_id'] ?? null;

        if (!$sectionId) {
            $response->getBody()->write(json_encode(['error' => 'Section ID is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $enrollmentModel = new \App\Models\Enrollment();
            $data = $enrollmentModel->getTotalPercentageBySectionId($sectionId);

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to fetch total percentages: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function submitRemarkRequest($request, $response)
    {
        $body = $request->getParsedBody();
        $assessmentId = $body['assessment_id'] ?? null;
        $studentId = $body['student_id'] ?? null;
        $justification = $body['justification'] ?? '';

        if (!$assessmentId || !$studentId || !$justification) {
            $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $remarkModel = new \App\Models\Remark();
            $remarkModel->submitRemarkRequest($assessmentId, $studentId, $justification);
            $response->getBody()->write(json_encode(['message' => 'Remark request submitted successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to submit remark request']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getNotifications($request, $response, $args)
    {
        $userId = $args['user_id'] ?? null;
        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $unreadOnly = $request->getQueryParams()['unread_only'] ?? false;
        $limit = $request->getQueryParams()['limit'] ?? 50;

        try {
            $notifications = $this->notificationModel->getByUserId($userId, $unreadOnly, $limit);
            $response->getBody()->write(json_encode($notifications));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to fetch notifications']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function markNotificationAsRead($request, $response, $args)
    {
        $userId = $args['user_id'] ?? null;
        $notifId = $args['notification_id'] ?? null;

        if (!$userId || !$notifId) {
            $response->getBody()->write(json_encode(['error' => 'User ID and Notification ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $this->notificationModel->markAsRead($notifId, $userId);
        $response->getBody()->write(json_encode(['success' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function markAllNotificationsAsRead($request, $response, $args)
    {
        $userId = $args['user_id'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $this->notificationModel->markAllAsRead($userId);
        $response->getBody()->write(json_encode(['success' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUnreadNotificationCount($request, $response, $args)
    {
        $userId = $args['user_id'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $count = $this->notificationModel->getUnreadCount($userId);
        $response->getBody()->write(json_encode(['count' => $count]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteNotification($request, $response, $args)
    {
        $userId = $args['user_id'] ?? null;
        $notifId = $args['notification_id'] ?? null;

        if (!$userId || !$notifId) {
            $response->getBody()->write(json_encode(['error' => 'User ID and Notification ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $this->notificationModel->delete($notifId, $userId);
        $response->getBody()->write(json_encode(['success' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
