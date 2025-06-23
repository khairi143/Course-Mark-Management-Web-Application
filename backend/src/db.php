<?php 
function getPDO() {
    $host = 'localhost';
    $db   = 'course_marks_db';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die('DB Connection failed: ' . $e->getMessage());
    }
    
} 

namespace App;

use PDO;
use PDOException;

class db {
    public function connect() {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=your_database", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
}