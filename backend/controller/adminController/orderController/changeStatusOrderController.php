<?php
include '../../../connection/dbconfig.php';
include '../../controller.php';
$database = new Database();
$set = new controller();
$db = $database->getDb();
$set->setCorsOrigin();
$data = $set->setInputData();

try {
    // customer_reference
    $msgQuery = "UPDATE `orders` SET `status` = :stat WHERE customer_reference = :cusref";
    $orderStmt = $db->prepare($msgQuery);
    $orderStmt->bindParam(":cusref", $data['product_id']); 
    $orderStmt->bindParam(":stat", $data['status']);
    $result = $orderStmt->execute();
    
    if($result){
        $payQuery = "UPDATE `payments` SET `payment_status`= :paymentStatus WHERE `payment_id` = :payId ";
        $payStmt = $db->prepare($payQuery);
        $payStmt->bindParam(":paymentStatus", $data['payment_status']); 
        $payStmt->bindParam(":payId", $data['payment_id']);
        $payStmt->execute();

        $set->sendJsonResponse(["success" => "Status Change Succesfully!"], 200);
    }else{
        $set->sendJsonResponse(["error" => "Status Change Failed!"], 400);
    }

} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
