<?php
include '../../../connection/dbconfig.php';
include '../../controller.php';
$database = new Database();
$set = new controller();
$db = $database->getDb();
$set->setCorsOrigin();
$data = $set->setInputData();

try {
    
    $query = "SELECT pay.payment_total, ord.extra FROM `orders` as ord INNER JOIN `payments` as pay ON ord.payment_id = pay.payment_id WHERE ord.status = 'delivered'";
    $stmt = $db->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $set->sendJsonResponse([
            "success" => true,
            "users" => $users
        ]);
    } else {
        $set->sendJsonResponse(["error" => "User not found"], 404);
    }

} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
