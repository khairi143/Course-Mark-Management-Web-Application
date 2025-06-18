<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class StudentAssessmentMark {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM student_assessment_marks");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM student_assessment_marks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByAssessment($assessment_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM student_assessment_marks WHERE assessment_id = ?");
        $stmt->execute([$assessment_id]);
        return $stmt->fetchAll();
    }

    public function create($student_id, $assessment_id, $marks_obtained) {
        $stmt = $this->pdo->prepare("
            INSERT INTO student_assessment_marks (student_id, assessment_id, marks_obtained) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$student_id, $assessment_id, $marks_obtained]);
    }

    public function update($id, $marks_obtained) {
        $stmt = $this->pdo->prepare("
            UPDATE student_assessment_marks SET marks_obtained = ? WHERE id = ?
        ");
        return $stmt->execute([$marks_obtained, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM student_assessment_marks WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
