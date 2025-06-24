<?php

namespace App\Controllers;

use App\Models\Enrollment;
use App\Models\Log;
use App\Models\User;
use App\Models\Advisor;
use App\Models\Course;
use App\Models\Section;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;

class AdminController
{
    private $enrollmentModel;
    private $logs;
    private $userModel;
    private $courseModel;
    private $advisorModel;
    private $sectionModel;

    public function __construct()
    {
        $this->enrollmentModel = new Enrollment();
        $this->logs = new Log();
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->advisorModel = new Advisor();
        $this->sectionModel = new Section();
    }

    // --- Enrollment Functions ---
    private function respondWithJson(Response $response, $data, int $status = 200): Response {
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
    public function getAllEnrollments(Request $request, Response $response, array $args): Response {
        $data = $this->enrollmentModel->getAll();
        return $this->respondWithJson($response, $data);
    }
    public function getEnrollment(Request $request, Response $response, array $args): Response {
        $data = $this->enrollmentModel->getById($args['id']);
        return $this->respondWithJson($response, $data);
    }
    public function createEnrollment(Request $request, Response $response, array $args): Response {
        $body = $request->getParsedBody();
        $success = $this->enrollmentModel->create($body['student_id'], $body['course_id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }
    public function updateEnrollment(Request $request, Response $response, array $args): Response {
        $body = $request->getParsedBody();
        $success = $this->enrollmentModel->update($args['id'], $body['student_id'], $body['course_id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }
    public function deleteEnrollment(Request $request, Response $response, array $args): Response {
        $success = $this->enrollmentModel->delete($args['id']);
        return $this->respondWithJson($response, ['success' => $success]);
    }

    // --- Logs Functions ---
    public function getLogs(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $logs = $this->logs->getAll($params);
        $response->getBody()->write(json_encode($logs));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function createLog(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $userId = $data['user_id'] ?? null;
        $action = $data['action'] ?? '';
        $details = $data['details'] ?? '';
        if (!$action) {
            $response->getBody()->write(json_encode(['error' => 'Action is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $this->logs->create($userId, $action, $details);
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function markLogAsReviewed(Request $request, Response $response, array $args): Response
    {
        $logId = $args['id'];
        $this->logs->markReviewed($logId);
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // --- User Functions ---
    public function getAllUsers(Request $request, Response $response, array $args): Response
    {
        $users = $this->userModel->getAllUsersWithDetails();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function createUser(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $this->userModel->createUser($data);
        $response->getBody()->write(json_encode(['message' => 'User created']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $this->userModel->updateUser($id, $data);
        $response->getBody()->write(json_encode(['message' => 'User updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $this->userModel->deleteUser($id);
        $response->getBody()->write(json_encode(['message' => 'User deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function resetPassword(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';

        if (empty($username)) {
            $payload = json_encode(['error' => 'Username required']);
            $stream = (new StreamFactory())->createStream($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400)
                ->withBody($stream);
        }

        $userModel = new User();
        $role = $userModel->getRoleByUsername($username);
        if (!$role) {
            $payload = json_encode(['error' => 'User not found']);
            $stream = (new StreamFactory())->createStream($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404)
                ->withBody($stream);
        }

        $newPassword = ($role['role_name'] === 'student') ? '11111' : 'utmbest';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $userModel->resetPasswordByUsername($username, $hashedPassword);

        if ($result) {
            $payload = json_encode([
                'message' => 'Password reset successfully',
                'new_password' => $newPassword
            ]);
            $stream = (new StreamFactory())->createStream($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->withBody($stream);
        } else {
            $payload = json_encode(['error' => 'User not found or update failed']);
            $stream = (new StreamFactory())->createStream($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404)
                ->withBody($stream);
        }
    }



    public function createCourse(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $course_code = $data['course_code'] ?? '';
        $course_name = $data['course_name'] ?? '';
        $department = $data['department'] ?? '';
        $sections = $data['sections'] ?? [];

        if (!$course_code || !$course_name || !$department) {
            return $this->respondWithJson($response, ['error' => 'All fields are required'], 400);
        }

        $success = $this->courseModel->createCourse($course_code, $course_name, $department, $sections);

        if ($success) {
            return $this->respondWithJson($response, ['message' => 'Course and sections created successfully']);
        } else {
            return $this->respondWithJson($response, ['error' => 'Failed to create course or sections'], 500);
        }
    }

    public function getAllCourse(Request $request, Response $response): Response
    {
        $courses = $this->courseModel->getAll();
        $response->getBody()->write(json_encode($courses));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateCourse(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $course_code = $data['course_code'] ?? '';
        $course_name = $data['course_name'] ?? '';
        $department = $data['department'] ?? '';
        $sections = $data['sections'] ?? [];

        if (!$course_code || !$course_name || !$department) {
            return $this->respondWithJson($response, ['error' => 'All fields are required'], 400);
        }

        $success = $this->courseModel->updateCourse($id, $course_code, $course_name, $department, $sections);

        if ($success) {
            return $this->respondWithJson($response, ['message' => 'Course and sections updated successfully']);
        } else {
            return $this->respondWithJson($response, ['error' => 'Failed to update course or sections'], 500);
        }
    }

    public function getAllAdvisor(Request $request, Response $response, $args)
    {
        try {
            $advisors = $this->advisorModel->getAll();
            $response->getBody()->write(json_encode($advisors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to fetch advisors']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getAdvisorById(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        try {
            $advisor = $this->advisorModel->getById($id);
            if ($advisor) {
                $response->getBody()->write(json_encode($advisor));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(['error' => 'Advisor not found']));
            }
        } catch (\Exception $e) {
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Failed to fetch advisor']));
        }
    }

    public function getSectionsByCourse($request, $response, $args)
    {
        $courseId = $args['course_id'];
        $sections = $this->sectionModel->getSectionsByCourse($courseId);

        $response->getBody()->write(json_encode($sections));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateLecturer($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $sectionId = $args['id'];
        $lecturerId = $data['lecturer_id'] ?? null;

        if (!$lecturerId) {
            $response->getBody()->write(json_encode(['error' => 'Missing lecturer_id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $success = $this->sectionModel->updateLecturer($sectionId, $lecturerId);

        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteCourse($request, $response, $args)
    {
        $id = $args['id'];
        $success = $this->courseModel->deleteCourse($id);

        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }


} 