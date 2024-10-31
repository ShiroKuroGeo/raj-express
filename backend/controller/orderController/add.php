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
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

try {

    if (!empty($data['orders'])) {
        $database = new Database();
        $db = $database->getDb();
    
        foreach ($data['orders'] as $order) {
            // Inserting The payment first 
            $payment = "INSERT INTO `payments`(`user_id`, `payment_method`, `payment_total`, `payment_status`) VALUES (:user_id, :payment_method, :payment_total, :payment_status)";
            $paymentStmt = $db->prepare($payment);
            $paymentStmt->bindParam(":user_id", $order['user_id']);
            $paymentStmt->bindParam(":payment_method", $order['payment_method']);
            $paymentStmt->bindParam(":payment_total", $order['payment_total']);
            $paymentStmt->bindParam(":payment_status", $order['payment_status']);
            $paymentStmt->execute();
            $paymentId = $db->lastInsertId();
    
            if ($paymentId) {
                // Placing Order then erase cart
                $orderQuery = "INSERT INTO `orders`(`user_id`, `product_id`, `address_id`, `payment_id`, `order_qty`, `extra`) VALUES (:user_id, :product_id, :address_id, :payment_id, :order_qty, :extra)";
                $orderStmt = $db->prepare($orderQuery);
                $orderStmt->bindParam(":user_id", $order['user_id']);
                $orderStmt->bindParam(":product_id", $order['product_id']);
                $orderStmt->bindParam(":address_id", $order['address_id']);
                $orderStmt->bindParam(":payment_id", $paymentId);
                $orderStmt->bindParam(":order_qty", $order['order_qty']);
                $orderStmt->bindParam(":extra", $order['extra']);
                $result = $orderStmt->execute();

                if($result){
                    $cartQuery = "UPDATE `carts` SET `status` = :stat WHERE cart_id = :cart_id";
                    $statusOrder = 'ordered';
                    $cartStmt = $db->prepare($cartQuery);
                    $cartStmt->bindParam(":cart_id", $order['cart_id']);
                    $cartStmt->bindParam(":stat", $statusOrder);
                    $cartResult = $cartStmt->execute();

                }else{
                    sendJsonResponse(["error" => "Failed to create payment record."], 409);
                }
                
            } else {
                sendJsonResponse(["error" => "Failed to create payment record."], 500);
            }
        }
        sendJsonResponse(["success" => "All orders processed successfully."], 200);
    } else {
        sendJsonResponse(["error" => "No orders found."], 400);
    }

} catch (Exception $e) {
    sendJsonResponse(["error" => "Error: " . $e->getMessage()], 500);
}
