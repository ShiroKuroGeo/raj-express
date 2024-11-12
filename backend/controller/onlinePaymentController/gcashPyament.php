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
    $paymentId = $data['payment_id'];
    $lineItems = [];
    foreach ($data['orders'] as $order) {
        $lineItems[] = [
            "currency" => "PHP",
            "amount" => $order['onlineTotal'],
            "description" => $order['description'],
            "name" => $order['name'],
            "quantity" => $order['quantity'],
        ];
    }
    
    $response = $client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
        'body' => json_encode([
            "data" => [
                "attributes" => [
                    "send_email_receipt" => false,
                    "show_description" => true,
                    "show_line_items" => true,
                    "line_items" => $lineItems,
                    "payment_method_types" => ["gcash"],
                    'success_url' => "http://localhost:9000/#/payment-success/$paymentId",
                    "description" => "test"
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

    if (isset($responseBody['data']['attributes'])) {
        $attributes = $responseBody['data']['attributes'];
        $set->sendJsonResponse(["success" => "Checkout session created successfully.", "data" => $attributes], 200);
    } else {
        $set->sendJsonResponse(["error" => "Missing attributes in API response."], 500);
    }

} catch (Exception $e) {
    $set->sendJsonResponse(["error" => "Error: " . $e->getMessage()], 500);
}
