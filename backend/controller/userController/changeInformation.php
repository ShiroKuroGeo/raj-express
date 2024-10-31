<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

include "../../connection/dbconfig.php"; 

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header("Content-Type: application/json");
    
    $json = json_encode($data);
    
    if ($json === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to encode JSON"]);
    } else {
        echo $json;
    }
    exit;
}

try {
    $database = new Database();
    $db = $database->getDb();

    if($data['password'] == null){
        $query = "UPDATE `users` SET `first_name` = :first_name, `last_name` = :last_name, `email` = :email, `contact_number` = :contact_number WHERE `user_id` = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":first_name", $data['firstName']);
        $stmt->bindParam(":last_name", $data['lastName']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":contact_number", $data['number']);
        $stmt->bindParam(":user_id", $data['user_id']);
        $result = $stmt->execute();

        if($result){
            sendJsonResponse(["success" => "Successfully Updated!"], 200);
        }else{
            sendJsonResponse(["error" => "Information not Updated!"], 400);
        }
    }else{
        $passHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "UPDATE `users` SET `first_name` = :first_name, `last_name` = :last_name, `email` = :email, `contact_number` = :contact_number, `password_hash` = :password  WHERE `user_id` = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":first_name", $data['firstName']);
        $stmt->bindParam(":last_name", $data['lastName']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":contact_number", $data['number']);
        $stmt->bindParam(":password", $passHash);
        $stmt->bindParam(":user_id", $data['user_id']);
        $result = $stmt->execute();

        if($result){
            sendJsonResponse(["success" => "Successfully Updated!"], 200);
        }else{
            sendJsonResponse(["error" => "Information not Updated!"], 400);
        }
    }

} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
