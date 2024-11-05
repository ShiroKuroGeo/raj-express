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
    $query = "SELECT cart.cart_id, cart.product_id, cart.user_id, cart.quantity, cart.status, cart.created_at, cart.updated_at, CONCAT(user.first_name, ' ', user.last_name) AS fullname, user.contact_number, user.email, product.product_name, product.product_price, product.product_image FROM `carts` as cart INNER JOIN `users` as user INNER JOIN `products` as product ON cart.user_id = user.user_id and cart.product_id = product.product_id WHERE cart.user_id = :user_id AND cart.status = 'pending'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $authHeader);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $set->sendJsonResponse([
            "success" => true,
            "cartItems" => $cart,
        ]);
    } else {
        $set->sendJsonResponse(["error" => "Cart not found"], 404);
    }
} catch (PDOException $e) {
    $set->sendJsonResponse(["error" => "Database error: " . $e->getMessage()], 500);
}
