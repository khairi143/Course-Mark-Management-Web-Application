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

    public function create($student_id, $course_id) {
        $stmt = $this->pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        return $stmt->execute([$student_id, $course_id]);
    }

    public function update($id, $student_id, $course_id) {
        $stmt = $this->pdo->prepare("UPDATE enrollments SET student_id = ?, course_id = ? WHERE id = ?");
        return $stmt->execute([$student_id, $course_id, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM enrollments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByCourse($course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE course_id = ?");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }

    public function getByStudent($student_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE student_id = ?");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
}
