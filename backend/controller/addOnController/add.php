<?php
include '../controller.php';

$set = new controller();

$set->setCorsOrigin();

$data = $set->setInputData();

include "../../connection/dbconfig.php"; 


try {
    $database = new Database();
    $db = $database->getDb();

    // Insert new cart item
    $query = "INSERT INTO `addons`(`ao_name`, `ao_price`, `ao_status`) VALUES (:aoname, :aoprice, :aostatus)";
    $stmt = $db->prepare($query);
    $pendingStatus = 'Available';
    $stmt->bindParam(":aoname", $data['aoname']);
    $stmt->bindParam(":aoprice", $data['aoprice']);
    $stmt->bindParam(":aostatus", $data['aostatus']);
    $stmt->bindParam(":status", $pendingStatus);

    if ($stmt->execute()) {
        $set->sendJsonResponse(["success" => "Successfully added to Add Ons"], 200);
    } else {
        $set->sendJsonResponse(["error" => "Failed to add to Add Ons"], 400);
    }
} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
