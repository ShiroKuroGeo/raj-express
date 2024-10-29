<?php
header("Access-Control-Allow-Origin: http://localhost:9000");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../connection/dbconfig.php';
header("Content-Type: application/json");

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Check if user is authenticated (you might want to implement a more robust auth check)
$authToken = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (empty($authToken)) {
    sendJsonResponse(["error" => "Unauthorized"], 401);
}

// Extract user ID from auth token (implement your own logic here)
$userId = /* extract user ID from auth token */'';

try {
    $database = new Database();
    $db = $database->getDb();

    $query = "SELECT first_name, last_name, email FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $userId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        sendJsonResponse([
            "success" => true,
            "first_name" => $user['first_name'],
            "last_name" => $user['last_name'],
            "email" => $user['email']
        ]);
    } else {
        sendJsonResponse(["error" => "User not found"], 404);
    }
} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
