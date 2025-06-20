<!-- src/models/AssessmentComponent.php -->
<?php

namespace App\Models;

use \PDO;

class AssessmentComponent {

    public static function getAll($courseId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM assessment_components WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM assessment_components WHERE component_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($courseId, $name, $weight, $maxMark) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO assessment_components (course_id, name, weight, max_mark) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courseId, $name, $weight, $maxMark]);
        return self::getById($pdo->lastInsertId());
    }

    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE assessment_components SET name = ?, weight = ?, max_mark = ? WHERE component_id = ?");
        $stmt->execute([$data['name'], $data['weight'], $data['max_mark'], $id]);
        return self::getById($id);
    }

    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM assessment_components WHERE component_id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
