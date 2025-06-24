<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Remark {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function submitRemarkRequest($assessment_id, $student_id, $justification) {
        $stmt = $this->pdo->prepare("INSERT INTO remark_requests (assessment_id, student_id, justification) VALUES (?, ?, ?)");
        return $stmt->execute([$assessment_id, $student_id, $justification]);
    }

    public function getRemarksBySectionId($section_id) {
        $sql = "
            SELECT rr.*, a.assessment_name, s.name AS student_name, u.full_name AS student_full_name, u.username AS student_matric_no
            FROM remark_requests rr
            JOIN assessments a ON rr.assessment_id = a.id
            JOIN students s ON rr.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE a.section_id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$section_id]);
        return $stmt->fetchAll();
    }

    public function getRemarksByStudentId($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT rr.*, a.assessment_name
            FROM remark_requests rr
            JOIN assessments a ON rr.assessment_id = a.id
            WHERE rr.student_id = ?
        ");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
}
