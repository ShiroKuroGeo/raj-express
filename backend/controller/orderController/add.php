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

    $order = "INSERT INTO `orders`(`user_id`, `product_id`, `address_id`, `payment_id`, `order_qty`, `extra`, `status`) VALUES (:user_id, :product_id, :address_id, :payment_id, :order_qty, :extra, :status)";
    $cartStmt = $db->prepare($order);
    // $cartStmt->bindParam(":status", $pendingStatus);
    $cartStmt->execute();

    

} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
