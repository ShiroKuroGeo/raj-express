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

    $query = "SELECT ord.order_id as id, ord.order_qty as qty, ord.extra, ord.status as orderStatus, pro.product_name as name, 
    pro.product_image as img, pro.product_price as price, pay.payment_method, pay.payment_total, pay.payment_status FROM 
    `orders` as ord INNER JOIN `users` as us INNER JOIN `products` as pro INNER JOIN `addresses` as 
    addr INNER JOIN `payments` AS pay ON ord.user_id = us.user_id AND ord.product_id = pro.product_id AND 
    ord.address_id = addr.address_id AND ord.payment_id = pay.payment_id WHERE ord.user_id = :user_id AND ord.status = 'pending'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse([
            "success" => true,
            "ordersItems" => $orders,
        ]);
    } else {
        sendResponse(["error" => "Cart not found"], 404);
    }
} catch (PDOException $e) {
    sendResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
