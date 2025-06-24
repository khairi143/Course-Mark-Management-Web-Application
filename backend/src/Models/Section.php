<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Section
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    /**
     * Update the lecturer for a section by section ID.
     *
     * @param int $sectionId
     * @param int $lecturerId
     * @return bool
     */
    public function updateLecturer($sectionId, $lecturerId)
    {
        try {
            $sql = "UPDATE sections SET lecturer_id = :lecturer_id WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'lecturer_id' => $lecturerId,
                'id' => $sectionId
            ]);
        } catch (\PDOException $e) {
            error_log("Failed to update section lecturer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all sections for a given course ID.
     *
     * @param int $courseId
     * @return array
     */
    public function getSectionsByCourse($courseId)
    {
        try {
            $sql = "SELECT * FROM sections WHERE course_id = :course_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['course_id' => $courseId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Failed to fetch sections: " . $e->getMessage());
            return [];
        }
    }
} 