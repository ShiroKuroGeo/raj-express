<?php
include '../../controller.php';
$set = new controller();

$set->setCorsOrigin();
$set->setInputData();
// $set->setConnection();


try {
    
    $set->sendJsonResponse(["success" => 'Successfully updated!'], 200);

} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
