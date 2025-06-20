<!-- src/models/AssessmentMark.php -->
<?php

namespace App\Models;

use \PDO;

class AssessmentMark {

    public static function getAll($enrollmentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM assessment_marks WHERE enrollment_id = ?");
        $stmt->execute([$enrollmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM assessment_marks WHERE mark_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($enrollmentId, $componentId, $markObtained) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO assessment_marks (enrollment_id, component_id, mark_obtained) VALUES (?, ?, ?)");
        $stmt->execute([$enrollmentId, $componentId, $markObtained]);
        return self::getById($pdo->lastInsertId());
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE assessment_marks SET mark_obtained = ? WHERE mark_id = ?");
        $stmt->execute([$data['mark_obtained'], $id]);
        return self::getById($id);
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM assessment_marks WHERE mark_id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
