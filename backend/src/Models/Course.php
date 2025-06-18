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

    /**
     * Update lecturer for a course by course ID.
     *
     * @param int $courseId
     * @param int $lecturerId
     * @return bool
     */
    public function updateLecturer(int $courseId, int $lecturerId): bool
    {
        try {
            $sql = "UPDATE courses SET lecturer_id = :lecturer_id WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'lecturer_id' => $lecturerId,
                'id' => $courseId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to update course lecturer: " . $e->getMessage());
            return false;
        }
    }
}
