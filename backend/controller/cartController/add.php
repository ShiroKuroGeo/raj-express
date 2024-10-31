<?php
include '../controller.php';

$set = new controller();

$set->setCorsOrigin();

$data = $set->setInputData();

include "../../connection/dbconfig.php"; 

try {
    $database = new Database();
    $db = $database->getDb();

    $checkCart = "SELECT `product_id`, `user_id` FROM `carts` WHERE `status` = :status AND `product_id` = :product_id AND `user_id` = :user_id";
    $cartStmt = $db->prepare($checkCart);
    $pendingStatus = 'pending';
    $cartStmt->bindParam(":status", $pendingStatus);
    $cartStmt->bindParam(":product_id", $data['product_id']);
    $cartStmt->bindParam(":user_id", $data['user_id']);
    $cartStmt->execute();

    if ($cartStmt->rowCount() > 0) {
        // If naay siyay cart daan ingun ani unya nakapending, kani sya ang mo gawas
        $set->sendJsonResponse(["success" => "Product already in your cart!"], 200);
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
            $set->sendJsonResponse(["success" => "Product successfully added to cart"], 200);
        } else {
            $set->sendJsonResponse(["error" => "Failed to add product to cart"], 400);
        }
    }

} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
