<?php
namespace App\Controllers;

use App\Models\Lecturer;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Assessment;
use App\Models\StudentAssessmentMark;
use App\Models\Notification;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LecturerController
{
    protected $lecturerModel;
    protected $courseModel;
    protected $studentModel;
    protected $enrollmentModel;
    protected $assessmentModel;

    public function __construct()
    {
        $this->lecturerModel = new Lecturer();
        $this->courseModel = new Course();
        $this->studentModel = new Student();
        $this->enrollmentModel = new Enrollment();
        $this->assessmentModel = new Assessment();
    }

    // GET /api/lecturers
    public function getAll(Request $request, Response $response): Response
    {
        $lecturers = $this->lecturerModel->getAll();
        $response->getBody()->write(json_encode($lecturers));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/{id}/courses-with-sections
    public function getCoursesWithSections(Request $request, Response $response, array $args): Response
    {
        $lecturer_id = $args['id'] ?? null;
        if (!$lecturer_id) {
            $response->getBody()->write(json_encode(['error' => 'Lecturer ID is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $courses = $this->courseModel->getCoursesByLecturerId($lecturer_id);
        $response->getBody()->write(json_encode($courses));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/by-user/{user_id}
    public function getLecturerIdByUserId(Request $request, Response $response, array $args): Response
    {
        $user_id = $args['user_id'] ?? null;
        if (!$user_id) {
            $response->getBody()->write(json_encode(['error' => 'User ID is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $lecturer_id = $this->lecturerModel->getLecturerIdByUserId($user_id);
        $response->getBody()->write(json_encode(['lecturer_id' => $lecturer_id]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/students
    public function getAllStudents(Request $request, Response $response): Response
    {
        $students = $this->studentModel->getAll();
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/students/{id}
    public function getStudentById(Request $request, Response $response, array $args): Response
    {
        $student = $this->studentModel->getById($args['id']);
        $response->getBody()->write(json_encode($student));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/lecturers/students
    public function createStudent(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $success = $this->studentModel->create($data);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/lecturers/students/{id}
    public function updateStudent(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $success = $this->studentModel->update($args['id'], $data);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/lecturers/students/{id}
    public function deleteStudent(Request $request, Response $response, array $args): Response
    {
        $success = $this->studentModel->delete($args['id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/enrollments
    public function getAllEnrollments(Request $request, Response $response): Response
    {
        $enrollments = $this->enrollmentModel->getAll();
        $response->getBody()->write(json_encode($enrollments));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/enrollments/{id}
    public function getEnrollmentById(Request $request, Response $response, array $args): Response
    {
        $enrollment = $this->enrollmentModel->getById($args['id']);
        $response->getBody()->write(json_encode($enrollment));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/lecturers/enrollments
    public function createEnrollment(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $success = $this->enrollmentModel->create($data['student_id'], $data['section_id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/lecturers/enrollments/{id}
    public function updateEnrollment(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $success = $this->enrollmentModel->update($args['id'], $data['student_id'], $data['section_id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/lecturers/enrollments/{id}
    public function deleteEnrollment(Request $request, Response $response, array $args): Response
    {
        $success = $this->enrollmentModel->delete($args['id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/sections/{section_id}/students
    public function getStudentsBySectionId(Request $request, Response $response, array $args): Response
    {
        $section_id = $args['section_id'] ?? null;
        if (!$section_id) {
            $response->getBody()->write(json_encode(['error' => 'Section ID is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $students = $this->enrollmentModel->getStudentsBySectionId($section_id);
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/lecturers/enrollments/by-student-section
    public function removeEnrollmentByStudentAndSection(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $student_id = $data['student_id'] ?? null;
        $section_id = $data['section_id'] ?? null;
        if (!$student_id || !$section_id) {
            $response->getBody()->write(json_encode(['error' => 'Student ID and Section ID are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $success = $this->enrollmentModel->removeByStudentAndSection($student_id, $section_id);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/assessments
    public function getAllAssessments(Request $request, Response $response): Response
    {
        $assessments = $this->assessmentModel->getAll();
        $response->getBody()->write(json_encode($assessments));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/assessments/{id}
    public function getAssessmentById(Request $request, Response $response, array $args): Response
    {
        $assessment = $this->assessmentModel->getById($args['id']);
        $response->getBody()->write(json_encode($assessment));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/sections/{section_id}/assessments
    public function getAssessmentsBySectionId(Request $request, Response $response, array $args): Response
    {
        $section_id = $args['section_id'] ?? null;
        if (!$section_id) {
            $response->getBody()->write(json_encode(['error' => 'Section ID is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $assessments = $this->assessmentModel->getBySectionId($section_id);
        $response->getBody()->write(json_encode($assessments));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/lecturers/assessments
    public function createAssessment(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $success = $this->assessmentModel->create($data['section_id'], $data['assessment_name'], $data['max_marks'], $data['weight_percentage']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/lecturers/assessments/{id}
    public function updateAssessment(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $success = $this->assessmentModel->update($args['id'], $data['assessment_name'], $data['max_marks'], $data['weight_percentage']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/lecturers/assessments/{id}
    public function deleteAssessment(Request $request, Response $response, array $args): Response
    {
        $success = $this->assessmentModel->delete($args['id']);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/lecturers/sections/{section_id}/students/{student_id}/assessment-marks
    public function saveStudentAssessmentMarks(Request $request, Response $response, array $args): Response
    {
        $section_id = $args['section_id'] ?? null;
        $student_id = $args['student_id'] ?? null;
        
        if (!$section_id || !$student_id) {
            $response->getBody()->write(json_encode(['error' => 'Section ID and Student ID are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $data = $request->getParsedBody(); // expects: [{ assessment_id, marks_obtained }]
        if (!is_array($data) || empty($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid data format']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $markModel = new StudentAssessmentMark();
        $notificationModel = new Notification();
        $assessmentModel = new Assessment();
        $results = [];
        $changedMarks = 0;
        $totalMarks = 0;

        foreach ($data as $item) {
            if (!isset($item['assessment_id'], $item['marks_obtained'])) continue;
            
            $totalMarks++;
            $result = $markModel->saveOrUpdateStudentAssessmentMark($student_id, $item['assessment_id'], $item['marks_obtained']);
            $results[] = $result;
            
            // Create notification only if the mark actually changed
            if ($result['success'] && $result['changed']) {
                $changedMarks++;
                // Get assessment details for notification
                $assessment = $assessmentModel->getById($item['assessment_id']);
                if ($assessment) {
                    $notificationModel->createMarkUpdateNotification(
                        $student_id, 
                        $item['assessment_id'], 
                        $section_id, 
                        $assessment['assessment_name']
                    );
                }
            }
        }

        $response->getBody()->write(json_encode([
            'success' => true, 
            'results' => $results,
            'summary' => [
                'total_marks_processed' => $totalMarks,
                'marks_changed' => $changedMarks,
                'marks_unchanged' => $totalMarks - $changedMarks
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/lecturers/sections/{section_id}/students/{student_id}/assessment-marks
    public function getStudentAssessmentMarks(Request $request, Response $response, array $args): Response
    {
        $section_id = $args['section_id'] ?? null;
        $student_id = $args['student_id'] ?? null;
        if (!$section_id || !$student_id) {
            $response->getBody()->write(json_encode(['error' => 'Section ID and Student ID are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $markModel = new StudentAssessmentMark();
        // To get all marks for this student in this section, get all assessments for the section, then get marks for each assessment
        $assessments = $this->assessmentModel->getBySectionId($section_id);
        $marks = [];
        foreach ($assessments as $assessment) {
            $assessment_id = $assessment['id'];
            $studentMarks = $markModel->getByAssessment($assessment_id);
            foreach ($studentMarks as $mark) {
                if ($mark['student_id'] == $student_id) {
                    $marks[] = $mark;
                }
            }
        }
        $response->getBody()->write(json_encode($marks));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/lecturers/sections/{section_id}/students/{student_id}/enrollment-total-percentage
    public function saveOrUpdateEnrollmentTotalPercentage(Request $request, Response $response, array $args): Response
    {
        $section_id = $args['section_id'] ?? null;
        $student_id = $args['student_id'] ?? null;
        $data = $request->getParsedBody();
        $total_percentage = $data['total_percentage'] ?? null;
        if (!$student_id || !$section_id || $total_percentage === null) {
            $response->getBody()->write(json_encode(['error' => 'Student ID, Section ID, and total_percentage are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $enrollmentModel = new \App\Models\Enrollment();
        $success = $enrollmentModel->saveOrUpdateTotalPercentage($student_id, $section_id, $total_percentage);
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * GET /api/lecturers/assessment-marks-join
     * Optional query params: student_id, assessment_id
     */
    public function getAssessmentMarksJoin($request, $response, $args) {
        $params = $request->getQueryParams();
        $student_id = isset($params['student_id']) ? $params['student_id'] : null;
        $assessment_id = isset($params['assessment_id']) ? $params['assessment_id'] : null;
        $markModel = new \App\Models\StudentAssessmentMark();
        $data = $markModel->getAssessmentMarksJoin($student_id, $assessment_id);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * GET /api/lecturers/total-percentage
     * Query params: student_id, section_id
     */
    public function getTotalPercentageByStudentIdAndSectionId($request, $response, $args) {
        $params = $request->getQueryParams();
        $student_id = isset($params['student_id']) ? $params['student_id'] : null;
        $section_id = isset($params['section_id']) ? $params['section_id'] : null;
        if (!$student_id || !$section_id) {
            $response->getBody()->write(json_encode(['error' => 'student_id and section_id are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $total = $this->enrollmentModel->getTotalPercentageByStudentIdAndSectionId($student_id, $section_id);
        $response->getBody()->write(json_encode(['total_percentage' => $total]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Helper method to get user_id from request (JWT token)
     */
    private function getUserIdFromRequest($request) {
        // This is a simplified version - you'll need to implement JWT token extraction
        // based on your authentication middleware
        $headers = $request->getHeaders();
        $authHeader = $headers['Authorization'] ?? null;
        
        if ($authHeader && is_array($authHeader) && !empty($authHeader[0])) {
            $token = str_replace('Bearer ', '', $authHeader[0]);
            // Decode JWT token and extract user_id
            // For now, return a placeholder - implement based on your JWT setup
            return 1; // Placeholder
        }
        
        return null;
    }

public function getRemarksBySection(Request $request, Response $response, array $args): Response
{
    $sectionId = $args['section_id'] ?? null;

    if (!$sectionId) {
        $payload = json_encode(['error' => 'Section ID is required']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        $remarkModel = new \App\Models\Remark();
        $remarks = $remarkModel->getRemarksBySectionId($sectionId);

        $payload = json_encode($remarks);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        $payload = json_encode(['error' => 'Failed to fetch remarks']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}

    
}
