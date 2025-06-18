<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class FinalExamMark {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM final_exam_marks");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM final_exam_marks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByCourse($course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM final_exam_marks WHERE course_id = ?");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }

    public function create($student_id, $course_id, $marks_obtained) {
        $stmt = $this->pdo->prepare("
            INSERT INTO final_exam_marks (student_id, course_id, marks_obtained)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$student_id, $course_id, $marks_obtained]);
    }

    public function update($id, $marks_obtained) {
        $stmt = $this->pdo->prepare("
            UPDATE final_exam_marks SET marks_obtained = ? WHERE id = ?
        ");
        return $stmt->execute([$marks_obtained, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM final_exam_marks WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
