<!-- // src/models/Lecturer.php -->
<?php

namespace App\Models;

use \PDO;

class Lecturer {

    // Get all lecturers
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM lecturers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a specific lecturer by ID
    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM lecturers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new lecturer
    public static function create($name, $email, $course_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO lecturers (name, email, course_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $course_id]);
        return self::getById($pdo->lastInsertId());  // Return the newly created lecturer
    }

    // Update an existing lecturer
    public static function update($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE lecturers SET name = ?, email = ?, course_id = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $data['course_id'], $id]);
        
        // Return the updated lecturer
        return self::getById($id);
    }

    // Delete a lecturer
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM lecturers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;  // Return true if any row was deleted
    }
}
