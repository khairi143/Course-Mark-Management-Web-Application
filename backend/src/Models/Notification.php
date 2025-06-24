<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    /**
     * Create a new notification
     *
     * @param int $user_id
     * @param string $title
     * @param string $message
     * @param string $type
     * @param int|null $related_assessment_id
     * @param int|null $related_section_id
     * @return bool
     */
    public function create($user_id, $title, $message, $type = 'general', $related_assessment_id = null, $related_section_id = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (user_id, title, message, type, related_assessment_id, related_section_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$user_id, $title, $message, $type, $related_assessment_id, $related_section_id]);
        } catch (\PDOException $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all notifications for a user
     *
     * @param int $user_id
     * @param bool $unread_only
     * @param int $limit
     * @return array
     */
    public function getByUserId($user_id, $unread_only = false, $limit = 50) {
        try {
            $limit = (int)$limit; // ensure it's an integer
    
            $sql = "SELECT * FROM notifications WHERE user_id = ?";
            $params = [$user_id];
    
            if ($unread_only) {
                $sql .= " AND is_read = FALSE";
            }
    
            $sql .= " ORDER BY created_at DESC LIMIT $limit"; // inject directly here
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Failed to get notifications: " . $e->getMessage());
            return [];
        }
    }
    
    

    /**
     * Mark a notification as read
     *
     * @param int $notification_id
     * @param int $user_id
     * @return bool
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET is_read = TRUE, read_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND user_id = ?
            ");
            return $stmt->execute([$notification_id, $user_id]);
        } catch (\PDOException $e) {
            error_log("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $user_id
     * @return bool
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET is_read = TRUE, read_at = CURRENT_TIMESTAMP 
                WHERE user_id = ? AND is_read = FALSE
            ");
            return $stmt->execute([$user_id]);
        } catch (\PDOException $e) {
            error_log("Failed to mark all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread count for a user
     *
     * @param int $user_id
     * @return int
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = ? AND is_read = FALSE
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            return $result ? (int)$result['count'] : 0;
        } catch (\PDOException $e) {
            error_log("Failed to get unread count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete a notification
     *
     * @param int $notification_id
     * @param int $user_id
     * @return bool
     */
    public function delete($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
            return $stmt->execute([$notification_id, $user_id]);
        } catch (\PDOException $e) {
            error_log("Failed to delete notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create notification for mark updates
     *
     * @param int $student_id
     * @param int $assessment_id
     * @param int $section_id
     * @param string $assessment_name
     * @return bool
     */
    public function createMarkUpdateNotification($student_id, $assessment_id, $section_id, $assessment_name) {
        try {
            // Get student's user_id
            $stmt = $this->pdo->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();
            
            if (!$student) {
                return false;
            }
            
            $title = "Assessment Mark Updated";
            $message = "Your mark for assessment '{$assessment_name}' has been updated. Please check your results.";
            
            return $this->create(
                $student['user_id'], 
                $title, 
                $message, 
                'mark_update', 
                $assessment_id, 
                $section_id
            );
        } catch (\PDOException $e) {
            error_log("Failed to create mark update notification: " . $e->getMessage());
            return false;
        }
    }
} 