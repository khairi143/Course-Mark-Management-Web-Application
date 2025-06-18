<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Assessment {
    protected $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM assessments");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM assessments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByCourseId($courseId) {
        $stmt = $this->pdo->prepare("SELECT * FROM assessments WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function create($course_id, $assessment_name, $max_marks, $weight_percentage) {
        $stmt = $this->pdo->prepare("INSERT INTO assessments (course_id, assessment_name, max_marks, weight_percentage) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$course_id, $assessment_name, $max_marks, $weight_percentage]);
    }

    public function update($id, $assessment_name, $max_marks, $weight_percentage) {
        $stmt = $this->pdo->prepare("UPDATE assessments SET assessment_name = ?, max_marks = ?, weight_percentage = ? WHERE id = ?");
        return $stmt->execute([$assessment_name, $max_marks, $weight_percentage, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM assessments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
