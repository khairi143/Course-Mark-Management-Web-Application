<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Log
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    // Create a log
    public function create($userId, $action, $details = '')
    {
        $stmt = $this->pdo->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $action, $details]);
    }

    // Get all logs (optionally filter by user or action)
    public function getAll($filter = [])
    {
        $query = "SELECT logs.*, users.full_name 
                  FROM logs 
                  LEFT JOIN users ON logs.user_id = users.id 
                  WHERE 1=1";

        $params = [];

        if (!empty($filter['user_id'])) {
            $query .= " AND user_id = ?";
            $params[] = $filter['user_id'];
        }

        if (!empty($filter['action'])) {
            $query .= " AND action = ?";
            $params[] = $filter['action'];
        }

        $query .= " ORDER BY logs.timestamp DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Mark log as reviewed
    public function markReviewed($logId)
    {
        $stmt = $this->pdo->prepare("UPDATE logs SET reviewed = 1 WHERE id = ?");
        return $stmt->execute([$logId]);
    }
}
