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

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

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

    // Request delete should be 14 days before the deletion process is complete
    // No data will be remove or delete, instead the user status is archive
    
    $query = "UPDATE `users` SET `date_deletion` = :delDate WHERE `user_id` = :user_id";
    $stmt = $db->prepare($query);
    $setDate = strtotime("+14 days", time());
    $date_deletion = date("Y-m-d H:i:s", $setDate);
    $stmt->bindParam(":delDate", $date_deletion);
    $stmt->bindParam(":user_id", $authHeader);  
    $result = $stmt->execute();

    if($result){
        sendJsonResponse(["success" => "Your account will be deleted in " .$date_deletion], 200);
    }else{
        sendJsonResponse(["error" => "Information not Updated!"], 400);
    }

} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
