<?php
include '../controller.php';

$set = new controller();

$set->setCorsOrigin();

$data = $set->setInputData();

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

include "../../connection/dbconfig.php"; 

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
        $set->sendJsonResponse([
            "success" => true,
            "ordersItems" => $orders,
        ]);
    } else {
        $set->sendJsonResponse(["error" => "Cart not found"], 404);
    }
} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
