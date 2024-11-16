<?php
include '../controller.php';

$set = new controller();
$set->setCorsOrigin();
$data = $set->setInputData();

include "../../connection/dbconfig.php";

$database = new Database();
$db = $database->getDb();

include "../../../vendor/autoload.php";

$client = new \GuzzleHttp\Client();

try {
    $database = new Database();
    $db = $database->getDb();

    // Insert payment record
    $payment = "INSERT INTO `payments`(`user_id`, `payment_method`, `payment_total`, `payment_status`) 
                VALUES (:user_id, :payment_method, :payment_total, :payment_status)";
    $paymentStmt = $db->prepare($payment);
    $paymentStmt->bindParam(":user_id",  $data['orders'][0]['user_id']);
    $paymentStmt->bindParam(":payment_method",  $data['orders'][0]['payment_method']);
    $paymentStmt->bindParam(":payment_total",  $data['orders'][0]['payment_total']);
    $paymentStmt->bindParam(":payment_status",  $data['orders'][0]['payment_status']);
    $paymentStmt->execute();
    $paymentId = $db->lastInsertId();
    $orderReference = 'cusord24' . $paymentId;
    
    $response = $client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
        'body' => json_encode([
            "data" => [
                "attributes" => [
                    "send_email_receipt" => false,
                    "show_description" => true,
                    "show_line_items" => true,
                    "line_items" => array_map(function($order) {
                        return [
                            "currency" => "PHP",
                            "amount" => $order['productTotal'], 
                            "description" => $order['description'],
                            "name" => $order['name'], 
                            "quantity" => $order['quantity'],
                        ];
                    }, $data['orders']),
                    "amount" => $data['orders'][0]['onlineTotal'],
                    "currency" => "PHP",
                    "payment_method_types" => ["gcash"],
                    'success_url' => "http://localhost:9000/#/payment-success/$paymentId",
                    "description" => "Custom total amount set manually for GCash",
                ]
            ]
        ]),
        'headers' => [
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => 'Basic c2tfdGVzdF84MWFRZGRMUHlEWWI1cXd2b2JGaWRwaEw6UmFqZXhwcmVzczIwMjQl',
        ],
    ]);

    $responseBody = json_decode($response->getBody(), true);

    if ($response->getStatusCode() !== 200) {
        error_log("PayMongo Response Error: " . json_encode($responseBody));
        $set->sendJsonResponse(["error" => "Failed to create checkout session", "details" => $responseBody], $response->getStatusCode());
        exit;
    }

    // Extract the checkout URL from the PayMongo response
    if (isset($responseBody['data']['attributes']['checkout_url'])) {
        $checkoutUrl = $responseBody['data']['attributes']['checkout_url'];
    } else {
        $set->sendJsonResponse(["error" => "Failed to get checkout URL."], 500);
        exit;
    }

    // If there are orders, insert them into the database
    if (!empty($data['orders'])) {
        foreach ($data['orders'] as $order) {
            if ($paymentId) {
                $extraJson = json_encode($order['extra']);

                // Insert order record
                $orderQuery = "INSERT INTO `orders`(`user_id`, `product_id`, `address_id`, `payment_id`, `customer_reference`, `order_qty`, `extra`) 
                               VALUES (:user_id, :product_id, :address_id, :payment_id, :customer_reference, :order_qty, :extra)";
                $orderStmt = $db->prepare($orderQuery);
                $orderStmt->bindParam(":user_id", $order['user_id']);
                $orderStmt->bindParam(":product_id", $order['product_id']);
                $orderStmt->bindParam(":address_id", $order['address_id']);
                $orderStmt->bindParam(":payment_id", $paymentId);
                $orderStmt->bindParam(":customer_reference", $orderReference);
                $orderStmt->bindParam(":order_qty", $order['order_qty']);
                $orderStmt->bindParam(":extra", $extraJson);
                $result = $orderStmt->execute();

                // If the order is successfully inserted, update the cart status
                if ($result) {
                    $cartQuery = "UPDATE `carts` SET `status` = :stat WHERE cart_id = :cart_id";
                    $statusOrder = 'order';
                    $cartStmt = $db->prepare($cartQuery);
                    $cartStmt->bindParam(":cart_id", $order['cart_id']);
                    $cartStmt->bindParam(":stat", $statusOrder);
                    $cartResult = $cartStmt->execute();
                } else {
                    $set->sendJsonResponse(["error" => "Failed to create order record."], 409);
                }
            } else {
                $set->sendJsonResponse(["error" => "Failed to create payment record."], 500);
            }
        }

        // Now send the response with the checkout_url
        $set->sendJsonResponse([
            "success" => $checkoutUrl, 
            "message" => "Order is successfully done!"
        ], 200);
    } else {
        $set->sendJsonResponse(["error" => "No orders found."], 400);
    }
} catch (\Throwable $th) {
    $set->sendJsonResponse(["error" => "Error: " . $th->getMessage()], 500);
}
