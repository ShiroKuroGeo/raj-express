
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

    $query = "INSERT INTO `addresses`(`user_id`, `personName`, `phoneNumber`, `latitude`, `longitude`, `deliveryAddress`, `streetNumber`, `landmark`) VALUES ( :user_id, :personName, :phoneNumber, :latitude, :longitude, :deliveryAddress, :streetNumber, :landmark)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":user_id", $data['user_id']);
    $stmt->bindParam(":personName", $data['personName']);
    $stmt->bindParam(":phoneNumber", $data['phoneNumber']);
    $stmt->bindParam(":latitude", $data['latitude']);
    $stmt->bindParam(":longitude",  $data['longitude']);
    $stmt->bindParam(":deliveryAddress",  $data['deliveryAddress']);
    $stmt->bindParam(":streetNumber",  $data['streetNumber']);
    $stmt->bindParam(":landmark",  $data['landmark']);

    if ($stmt->execute()) {
        sendJsonResponse(["success" => "Successfully add address!"], 200);
    } else {
        sendJsonResponse(["error" => "Failed to add address"], 400);
    }
} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
