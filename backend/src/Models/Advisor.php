<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Advisor {
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT id, advisor_full_name FROM advisors");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM advisors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getIdByUserId($userId) {
        $stmt = $this->pdo->prepare("SELECT id FROM advisors WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getAdviseesById($userId)
    {
        // Step 1: Get advisor by user ID
        $stmt = $this->pdo->prepare("SELECT id FROM advisors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $advisor = $stmt->fetch();

        if (!$advisor) {
            return []; // or throw an exception if advisor not found
        }

        // Step 2: Use advisor ID to get students
        $stmt = $this->pdo->prepare("
            SELECT id, name, matric_number, email, phone, address
            FROM students
            WHERE advisor_id = ?
        ");
        $stmt->execute([$advisor['id']]);

        return $stmt->fetchAll();
    }

    public function getAdviseesMarksByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                s.id AS student_id,
                s.name AS student_name,
                s.matric_number,
                s.email,
                c.course_name,
                a.assessment_name,
                a.max_marks,
                a.weight_percentage,
                sam.marks_obtained,
                sam.submission_date,
                sec.section_name,
                e.total_percentage
            FROM advisors ad
            JOIN students s ON s.advisor_id = ad.id
            LEFT JOIN enrollments e ON e.student_id = s.id
            LEFT JOIN sections sec ON sec.id = e.section_id
            LEFT JOIN courses c ON c.id = sec.course_id
            LEFT JOIN assessments a ON a.section_id = sec.id
            LEFT JOIN student_assessment_marks sam ON sam.assessment_id = a.id AND sam.student_id = s.id
            WHERE ad.user_id = ?
            ORDER BY s.id, c.course_name, a.assessment_name
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAdvisorNotesByStudentId($studentId, $advisorId)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, student_id, title, content, date, created_at
            FROM advisor_notes
            WHERE student_id = ? AND advisor_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$studentId, $advisorId]);
        return $stmt->fetchAll();
    }

    public function addAdvisorNote($advisorId, $studentId, $title, $content, $date)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO advisor_notes (advisor_id, student_id, title, content, date)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$advisorId, $studentId, $title, $content, $date]);
        return ['message' => 'Note added successfully'];
    }

    public function updateAdvisorNote($noteId, $advisorId, $title, $content, $date)
    {
        $stmt = $this->pdo->prepare("
            UPDATE advisor_notes
            SET title = ?, content = ?, date = ?
            WHERE id = ? AND advisor_id = ?
        ");
        return $stmt->execute([$title, $content, $date, $noteId, $advisorId]);
    }

    public function deleteAdvisorNote($noteId, $advisorId)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM advisor_notes
            WHERE id = ? AND advisor_id = ?
        ");
        return $stmt->execute([$noteId, $advisorId]);
    }


}
