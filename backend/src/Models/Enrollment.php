<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Enrollment {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($student_id, $section_id) {
        $stmt = $this->pdo->prepare("INSERT INTO enrollments (student_id, section_id) VALUES (?, ?)");
        return $stmt->execute([$student_id, $section_id]);
    }

    public function update($id, $student_id, $section_id) {
        $stmt = $this->pdo->prepare("UPDATE enrollments SET student_id = ?, section_id = ? WHERE id = ?");
        return $stmt->execute([$student_id, $section_id, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM enrollments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByCourse($section_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE section_id = ?");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }

    public function getByStudent($student_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE student_id = ?");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }

    public function getStudentsBySectionId($section_id)
    {
        $sql = "SELECT s.id, s.matric_number, s.name, s.email, s.phone, u.full_name
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                JOIN users u ON s.user_id = u.id
                WHERE e.section_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$section_id]);
        return $stmt->fetchAll();
    }

    public function removeByStudentAndSection($student_id, $section_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM enrollments WHERE student_id = ? AND section_id = ?");
        return $stmt->execute([$student_id, $section_id]);
    }

    public function saveOrUpdateTotalPercentage($student_id, $section_id, $total_percentage) {
        $stmt = $this->pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND section_id = ? LIMIT 1");
        $stmt->execute([$student_id, $section_id]);
        $row = $stmt->fetch();
        if ($row) {
            $update = $this->pdo->prepare("UPDATE enrollments SET total_percentage = ? WHERE student_id = ? AND section_id = ?");
            return $update->execute([$total_percentage, $student_id, $section_id]);
        } else {
            $insert = $this->pdo->prepare("INSERT INTO enrollments (student_id, section_id, total_percentage) VALUES (?, ?, ?)");
            return $insert->execute([$student_id, $section_id, $total_percentage]);
        }
    }

    /**
     * Get total percentage for a specific student in a specific section.
     *
     * @param int $student_id
     * @param int $section_id
     * @return float|null
     */
    public function getTotalPercentageByStudentIdAndSectionId($student_id, $section_id) {
        $stmt = $this->pdo->prepare("SELECT total_percentage FROM enrollments WHERE student_id = ? AND section_id = ? LIMIT 1");
        $stmt->execute([$student_id, $section_id]);
        $row = $stmt->fetch();
        return $row ? $row['total_percentage'] : null;
    }

    public function getTotalPercentageBySectionId($section_id) {
        $stmt = $this->pdo->prepare("
            SELECT student_id, total_percentage 
            FROM enrollments 
            WHERE section_id = ? AND total_percentage IS NOT NULL
        ");
        $stmt->execute([$section_id]);
        return $stmt->fetchAll();
    }
    
}
