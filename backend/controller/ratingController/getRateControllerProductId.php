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
    
    $query = "SELECT * FROM `ratings` WHERE `product_id` = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $set->sendJsonResponse([
            "success" => true,
            "ratings" => $ratings,
        ]);

    } else {
        $set->sendJsonResponse(["error" => "No ratings found for this product"], 404);
    }
    
} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
