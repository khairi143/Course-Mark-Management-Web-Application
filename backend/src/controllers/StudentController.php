<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST');

require_once __DIR__ . '/../db.php'; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getAllStudents($conn);
        break;

    case 'POST':
        addStudent($conn);
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}

function getAllStudents($conn) {
    $sql = "SELECT * FROM students  "; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $students]);
    } else {
        echo json_encode(['status' => 'success', 'data' => []]);
    }
}

function addStudent($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['name'], $input['email'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        return;
    }

    $name = $conn->real_escape_string($input['name']);
    $email = $conn->real_escape_string($input['email']);

    $sql = "INSERT INTO students (name, email) VALUES ('$name', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Student added']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
