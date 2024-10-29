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

    // Checking for existing cart item and or pending
    $checkCart = "SELECT `product_id`, `user_id` FROM `carts` WHERE `status` = :status AND `product_id` = :product_id AND `user_id` = :user_id";
    $cartStmt = $db->prepare($checkCart);
    $pendingStatus = 'pending';
    $cartStmt->bindParam(":status", $pendingStatus);
    $cartStmt->bindParam(":product_id", $data['product_id']);
    $cartStmt->bindParam(":user_id", $data['user_id']);
    $cartStmt->execute();

    if ($cartStmt->rowCount() > 0) {
        // If naay siyay cart daan ingun ani unya nakapending, kani sya ang mo gawas
        sendJsonResponse(["success" => "Product already in your cart!"], 200);
    }else{
        // If wala or dili na pending, mo add siya as pending bago kong mo order siya balik
        $addOnsDataJson = json_encode($data['addOnsData']);
    
        $query = "INSERT INTO `carts` (`product_id`, `user_id`, `quantity`, `addOns`, `addOnsData`, `status`) VALUES (:product_id, :user_id, :quantity, :addOns, :addOnsData, :status)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":product_id", $data['product_id']);
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":quantity", $data['quantity']);
        $stmt->bindParam(":addOns", $data['addOns']);
        $stmt->bindParam(":addOnsData", $addOnsDataJson);
        $stmt->bindParam(":status", $pendingStatus);
    
        if ($stmt->execute()) {
            sendJsonResponse(["success" => "Product successfully added to cart"], 200);
        } else {
            sendJsonResponse(["error" => "Failed to add product to cart"], 400);
        }
    }

} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
