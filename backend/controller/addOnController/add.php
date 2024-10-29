<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

include "../../connection/dbconfig.php"; 

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header("Content-Type: application/json");
    
    $json = json_encode($data);
    
    if ($json === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to encode JSON"]);
    } else {
        echo $json;
    }
    exit;
}

try {
    $database = new Database();
    $db = $database->getDb();

    // Insert new cart item
    $query = "INSERT INTO `addons`(`ao_name`, `ao_price`, `ao_status`) VALUES (:aoname, :aoprice, :aostatus)";
    $stmt = $db->prepare($query);
    $pendingStatus = 'Available';
    $stmt->bindParam(":aoname", $data['aoname']);
    $stmt->bindParam(":aoprice", $data['aoprice']);
    $stmt->bindParam(":aostatus", $data['aostatus']);
    $stmt->bindParam(":status", $pendingStatus);

    if ($stmt->execute()) {
        sendJsonResponse(["success" => "Successfully added to Add Ons"], 200);
    } else {
        sendJsonResponse(["error" => "Failed to add to Add Ons"], 400);
    }
} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
