<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Role
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAllRoles()
    {
        $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getRoleById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
