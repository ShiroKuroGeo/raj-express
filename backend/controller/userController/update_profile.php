<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../connection/dbconfig.php"; 

function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
}

$response = [];

try {
    $database = new Database();
    $db = $database->getDb();

    $query = "SELECT * FROM `users` WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $address = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse([
            "success" => true,
            "addressItems" => $address,
        ]);
    } else {
        sendResponse(["error" => "Address not found"], 404);
    }
} catch (PDOException $e) {
    sendResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
