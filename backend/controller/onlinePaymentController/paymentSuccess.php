<?php
include '../controller.php';

$set = new controller();
$set->setCorsOrigin();
$data = $set->setInputData();

include "../../connection/dbconfig.php";

$database = new Database();
$db = $database->getDb();

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

try {
    $database = new Database();
    $db = $database->getDb();

    $payment = "UPDATE `payments` SET `payment_status` = 'Paid on Online' WHERE `payment_id` = :paymentid";
    $paymentStmt = $db->prepare($payment);
    $paymentStmt->bindParam(':paymentid', $authHeader);
    $result = $paymentStmt->execute();

    if($result){
        $set->sendJsonResponse(["success" => "Payment Successfully"], 200);
    }else{
        $set->sendJsonResponse(["failed" => "Payment Failed"], 401);
    }
} catch (\Throwable $th) {
    $set->sendJsonResponse(["error" => "Error: " . $th->getMessage()], 500);
}