<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../connection/dbconfig.php"; 

function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
}

$response = [];

try {
    $database = new Database();
    $db = $database->getDb();
    $query = "SELECT cart.cart_id, cart.addOns, cart.product_id, cart.addOnsData, cart.addOns, cart.user_id, cart.quantity, cart.status, cart.created_at, cart.updated_at, CONCAT(user.first_name, ' ', user.last_name) AS fullname, user.contact_number, user.email, product.product_name, product.product_price, product.product_image FROM `carts` as cart INNER JOIN `users` as user INNER JOIN `products` as product ON cart.user_id = user.user_id and cart.product_id = product.product_id WHERE cart.user_id = :user_id AND cart.status = 'pending'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse([
            "success" => true,
            "cartItems" => $cart,
        ]);
    } else {
        sendResponse(["error" => "Cart not found"], 404);
    }
} catch (PDOException $e) {
    sendResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
