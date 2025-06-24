<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Course
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    /**
     * Get all courses.
     *
     * @return array
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM courses");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Failed to fetch courses: " . $e->getMessage());
            return [];
        }
    }

    public function createCourse($course_code, $course_name, $department, $sections = [])
    {
        try {
            $this->pdo->beginTransaction();
            $sql = "INSERT INTO courses (course_code, course_name, department) VALUES (:course_code, :course_name, :department)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'course_code' => $course_code,
                'course_name' => $course_name,
                'department' => $department
            ]);
            $course_id = $this->pdo->lastInsertId();
            if ($sections && is_array($sections)) {
                $sectionSql = "INSERT INTO sections (course_id, section_name, max_capacity) VALUES (:course_id, :section_name, :max_capacity)";
                $sectionStmt = $this->pdo->prepare($sectionSql);
                foreach ($sections as $section) {
                    $section_name = is_array($section) ? ($section['section_name'] ?? '') : $section;
                    $max_capacity = is_array($section) ? ($section['max_capacity'] ?? null) : null;
                    $sectionStmt->execute([
                        'course_id' => $course_id,
                        'section_name' => $section_name,
                        'max_capacity' => $max_capacity
                    ]);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log('Failed to create course and sections: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCourse($id, $course_code, $course_name, $department, $sections = [])
    {
        try {
            $this->pdo->beginTransaction();
            // Update course
            $sql = "UPDATE courses SET course_code = :course_code, course_name = :course_name, department = :department WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'course_code' => $course_code,
                'course_name' => $course_name,
                'department' => $department
            ]);

            // Fetch existing section IDs
            $existingSections = $this->pdo->prepare("SELECT id FROM sections WHERE course_id = :course_id");
            $existingSections->execute(['course_id' => $id]);
            $existingIds = array_column($existingSections->fetchAll(\PDO::FETCH_ASSOC), 'id');
            $submittedIds = array_filter(array_column($sections, 'id'));

            // Delete removed sections
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                $in = str_repeat('?,', count($toDelete) - 1) . '?';
                $delStmt = $this->pdo->prepare("DELETE FROM sections WHERE id IN ($in)");
                $delStmt->execute(array_values($toDelete));
            }

            // Update or insert sections
            foreach ($sections as $section) {
                if (!empty($section['id']) && in_array($section['id'], $existingIds)) {
                    // Update
                    $updateStmt = $this->pdo->prepare("UPDATE sections SET section_name = :section_name, max_capacity = :max_capacity WHERE id = :id");
                    $updateStmt->execute([
                        'id' => $section['id'],
                        'section_name' => $section['section_name'],
                        'max_capacity' => $section['max_capacity']
                    ]);
                } else {
                    // Insert
                    $insertStmt = $this->pdo->prepare("INSERT INTO sections (course_id, section_name, max_capacity) VALUES (:course_id, :section_name, :max_capacity)");
                    $insertStmt->execute([
                        'course_id' => $id,
                        'section_name' => $section['section_name'],
                        'max_capacity' => $section['max_capacity']
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log('Failed to update course and sections: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all courses (with sections) assigned to a lecturer by lecturer_id.
     * Each course includes only the sections assigned to the lecturer.
     * @param int $lecturer_id
     * @return array
     */
    public function getCoursesByLecturerId($lecturer_id)
    {
        try {
            $sql = "SELECT c.id as course_id, c.course_code, c.course_name, c.department, s.id as section_id, s.section_name, s.max_capacity
                    FROM courses c
                    JOIN sections s ON c.id = s.course_id
                    WHERE s.lecturer_id = :lecturer_id
                    ORDER BY c.id, s.id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['lecturer_id' => $lecturer_id]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Group by course
            $courses = [];
            foreach ($rows as $row) {
                $cid = $row['course_id'];
                if (!isset($courses[$cid])) {
                    $courses[$cid] = [
                        'course_id' => $cid,
                        'course_code' => $row['course_code'],
                        'course_name' => $row['course_name'],
                        'department' => $row['department'],
                        'sections' => []
                    ];
                }
                $courses[$cid]['sections'][] = [
                    'section_id' => $row['section_id'],
                    'section_name' => $row['section_name'],
                    'max_capacity' => $row['max_capacity']
                ];
            }
            return array_values($courses);
        } catch (\PDOException $e) {
            error_log('Failed to fetch courses by lecturer: ' . $e->getMessage());
            return [];
        }
    }

    public function getCoursesByStudentId($student_id)
    {
        try {
            $sql = "SELECT 
                        c.id AS course_id, 
                        c.course_code, 
                        c.course_name, 
                        c.department,
                        s.id AS section_id, 
                        s.section_name, 
                        s.max_capacity
                    FROM enrollments e
                    JOIN sections s ON e.section_id = s.id
                    JOIN courses c ON s.course_id = c.id
                    WHERE e.student_id = :student_id
                    ORDER BY c.id, s.id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['student_id' => $student_id]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $courses = [];
            foreach ($rows as $row) {
                $cid = $row['course_id'];
                if (!isset($courses[$cid])) {
                    $courses[$cid] = [
                        'course_id' => $cid,
                        'course_code' => $row['course_code'],
                        'course_name' => $row['course_name'],
                        'department' => $row['department'],
                        'sections' => []
                    ];
                }
                $courses[$cid]['sections'][] = [
                    'section_id' => $row['section_id'],
                    'section_name' => $row['section_name'],
                    'max_capacity' => $row['max_capacity']
                ];
            }

            return array_values($courses);
        } catch (\PDOException $e) {
            error_log('Failed to fetch courses by student: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteCourse($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Delete sections related to the course
            $stmt1 = $this->pdo->prepare("DELETE FROM sections WHERE course_id = :id");
            $stmt1->execute(['id' => $id]);

            // Then delete the course
            $stmt2 = $this->pdo->prepare("DELETE FROM courses WHERE id = :id");
            $stmt2->execute(['id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log('Failed to delete course: ' . $e->getMessage());
            return false;
        }
    }



}
