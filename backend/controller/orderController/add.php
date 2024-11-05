<?php
include '../controller.php';

$set = new controller();

$set->setCorsOrigin();

$data = $set->setInputData();

include "../../connection/dbconfig.php"; 

try {

    if (!empty($data['orders'])) {
        $database = new Database();
        $db = $database->getDb();
    
        $payment = "INSERT INTO `payments`(`user_id`, `payment_method`, `payment_total`, `payment_status`) VALUES (:user_id, :payment_method, :payment_total, :payment_status)";
        $paymentStmt = $db->prepare($payment);
        $paymentStmt->bindParam(":user_id",  $data['orders'][0]['user_id']);
        $paymentStmt->bindParam(":payment_method",  $data['orders'][0]['payment_method']);
        $paymentStmt->bindParam(":payment_total",  $data['orders'][0]['payment_total']);
        $paymentStmt->bindParam(":payment_status",  $data['orders'][0]['payment_status']);
        $paymentStmt->execute();
        $paymentId = $db->lastInsertId();
        $orderReference = 'cusord24' . $paymentId;

        foreach ($data['orders'] as $order) {
            if ($paymentId) {
                $extraJson = json_encode($order['extra']);

                $orderQuery = "INSERT INTO `orders`(`user_id`, `product_id`, `address_id`, `payment_id`, `customer_reference`, `order_qty`, `extra`) VALUES (:user_id, :product_id, :address_id, :payment_id, :customer_reference, :order_qty, :extra)";
                $orderStmt = $db->prepare($orderQuery);
                $orderStmt->bindParam(":user_id", $order['user_id']);
                $orderStmt->bindParam(":product_id", $order['product_id']);
                $orderStmt->bindParam(":address_id", $order['address_id']);
                $orderStmt->bindParam(":payment_id", $paymentId);
                $orderStmt->bindParam(":customer_reference", $orderReference);
                $orderStmt->bindParam(":order_qty", $order['order_qty']);
                $orderStmt->bindParam(":extra", $extraJson);
                $result = $orderStmt->execute();

                if($result){
                    $cartQuery = "UPDATE `carts` SET `status` = :stat WHERE cart_id = :cart_id";
                    $statusOrder = 'order';
                    $cartStmt = $db->prepare($cartQuery);
                    $cartStmt->bindParam(":cart_id", $order['cart_id']);
                    $cartStmt->bindParam(":stat", $statusOrder);
                    $cartResult = $cartStmt->execute();
                }else{
                    $set->sendJsonResponse(["error" => "Failed to create payment record."], 409);
                }
                
            } else {
                $set->sendJsonResponse(["error" => "Failed to create payment record."], 500);
            }
        }
        $set->sendJsonResponse(["success" => "All orders processed successfully."], 200);
    } else {
        $set->sendJsonResponse(["error" => "No orders found."], 400);
    }

} catch (Exception $e) {
    $set->sendJsonResponse(["error" => "Error: " . $e->getMessage()], 500);
}
