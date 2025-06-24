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

    // Save or update a student's assessment mark
    public function saveOrUpdateStudentAssessmentMark($student_id, $assessment_id, $marks_obtained) {
        // Check if a mark already exists for this student and assessment
        $stmt = $this->pdo->prepare("SELECT id, marks_obtained FROM student_assessment_marks WHERE student_id = ? AND assessment_id = ?");
        $stmt->execute([$student_id, $assessment_id]);
        $row = $stmt->fetch();
        
        if ($row) {
            // Check if the mark has actually changed
            if ($row['marks_obtained'] == $marks_obtained) {
                // No change, return true but indicate no change
                return ['success' => true, 'changed' => false, 'action' => 'no_change'];
            }
            
            // Update existing - mark has changed
            $update = $this->pdo->prepare("UPDATE student_assessment_marks SET marks_obtained = ? WHERE id = ?");
            $result = $update->execute([$marks_obtained, $row['id']]);
            return ['success' => $result, 'changed' => true, 'action' => 'updated'];
        } else {
            // Insert new
            $insert = $this->pdo->prepare("INSERT INTO student_assessment_marks (student_id, assessment_id, marks_obtained) VALUES (?, ?, ?)");
            $result = $insert->execute([$student_id, $assessment_id, $marks_obtained]);
            return ['success' => $result, 'changed' => true, 'action' => 'inserted'];
        }
    }

    /**
     * Get existing mark for a student and assessment
     *
     * @param int $student_id
     * @param int $assessment_id
     * @return array|null
     */
    public function getExistingMark($student_id, $assessment_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM student_assessment_marks WHERE student_id = ? AND assessment_id = ?");
        $stmt->execute([$student_id, $assessment_id]);
        return $stmt->fetch();
    }

    /**
     * Get joined assessment and student_assessment_marks data.
     * Optionally filter by student_id and/or assessment_id.
     *
     * @param int|null $student_id
     * @param int|null $assessment_id
     * @return array
     */
    public function getAssessmentMarksJoin($student_id = null, $assessment_id = null) {
        $sql = "SELECT sam.*, a.assessment_name, a.max_marks, a.weight_percentage, a.section_id
                FROM student_assessment_marks sam
                JOIN assessments a ON sam.assessment_id = a.id
                WHERE 1=1";
        $params = [];
        if ($student_id !== null) {
            $sql .= " AND sam.student_id = ?";
            $params[] = $student_id;
        }
        if ($assessment_id !== null) {
            $sql .= " AND sam.assessment_id = ?";
            $params[] = $assessment_id;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAssessmentMarksByStudentAndSection($student_id, $section_id) {
        $sql = "SELECT sam.*, 
                       a.assessment_name, 
                       a.max_marks, 
                       a.weight_percentage, 
                       a.section_id
                FROM student_assessment_marks sam
                JOIN assessments a ON sam.assessment_id = a.id
                WHERE sam.student_id = :student_id
                  AND a.section_id = :section_id";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'student_id' => $student_id,
            'section_id' => $section_id
        ]);
    
        return $stmt->fetchAll();
    }

    /**
     * Get all students' assessment marks for a given section.
     *
     * @param int $section_id
     * @return array
     */
    public function getStudentsAssessmentMarksBySection($section_id) {
        $sql = "SELECT sam.student_id,
                    sam.assessment_id,
                    sam.marks_obtained,
                    a.assessment_name,
                    a.max_marks,
                    a.weight_percentage,
                    a.section_id
                FROM student_assessment_marks sam
                JOIN assessments a ON sam.assessment_id = a.id
                WHERE a.section_id = :section_id
                ORDER BY sam.student_id, a.assessment_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['section_id' => $section_id]);
        return $stmt->fetchAll();
    }

    
}
