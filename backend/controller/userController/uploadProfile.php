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

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

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

    $uploadDir = '../../uploads/';
    $filename = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
    $uploadFile = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $response['status'] = 'success';

        $query = "UPDATE `users` SET `profile_img` = :profile_img WHERE `user_id` = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $authHeader);
        $stmt->bindParam(":profile_img", $filename);
        $result = $stmt->execute();

        if($result){
            sendJsonResponse(["success" => "Image Uploaded!"], 200);
        }else{
            sendJsonResponse(["notFound" => "File image not found!"], 409);
        }

    } else {
        sendJsonResponse(["error" => "No file uploaded or upload error."], 400);
    }
    

} catch (PDOException $e) {
    sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}

?>
