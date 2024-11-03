<?php
include '../../../connection/dbconfig.php';
include '../../controller.php';
$database = new Database();
$set = new controller();
$db = $database->getDb();
$set->setCorsOrigin();
$data = $set->setInputData();
$headers = apache_request_headers();
// $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : 8;

try {
    
    $query = "SELECT ord.order_id, ord.order_qty as qty, ord.extra, ord.status, ord.created_at, prod.product_name, prod.product_image, address.personName as addressContactPerson, address.phoneNumber as addressContactNumber, address.deliveryAddress, address.streetNumber, address.landmark, pay.payment_method, pay.payment_total, pay.payment_status FROM `orders` as ord INNER JOIN `products` as prod INNER JOIN `addresses` as address INNER JOIN `payments` as pay INNER JOIN `users` as us ON ord.user_id = us.user_id AND ord.product_id = prod.product_id AND ord.address_id = address.address_id AND ord.payment_id = pay.payment_id WHERE ord.order_id = :order_id;";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($orderDetails as &$order) {
            $order['extra'] = json_decode($order['extra'], true);
        }
    
        $set->sendJsonResponse([
            "success" => true,
            "orderDetails" => $orderDetails,
        ]);
    } else {
        $set->sendJsonResponse(["error" => "Order Details not found"], 404);
    }

} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
