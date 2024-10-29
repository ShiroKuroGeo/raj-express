<?php
// At the very beginning of the file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

// Set headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse([], 200);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(["error" => "Method not allowed"], 405);
}

try {
    include_once '../connection/dbconfig.php';

    // Log the inclusion of dbconfig.php
    error_log("dbconfig.php included successfully");

    // Get the posted data
    $input = file_get_contents("php://input");
    error_log("Received raw input: " . $input);

    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        sendJsonResponse(["error" => "Invalid JSON input: " . json_last_error_msg()], 400);
    }

    error_log("Decoded data: " . print_r($data, true));

    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'email', 'password', 'user_type', 'status'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(["error" => "$field is required"], 400);
        }
    }

    // Create database connection
    $database = new Database();
    $db = $database->getDb();

    // Check if email already exists
    $check_email_query = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
    $check_stmt = $db->prepare($check_email_query);
    $check_stmt->bindParam(":email", $data['email']);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        sendJsonResponse(["error" => "Email already exists"], 400);
    }

    // Prepare the SQL query for insertion
    $query = "INSERT INTO users (first_name, last_name, address, contact_number, email, password_hash, user_type, status)
          VALUES (:first_name, :last_name, :address, :contact_number, :email, :password_hash, :user_type, :status)";

    $stmt = $db->prepare($query);

    // Hash the password
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Bind the parameters
    $stmt->bindParam(":first_name", $data['first_name']);
    $stmt->bindParam(":last_name", $data['last_name']);
    $stmt->bindParam(":address", $data['address']);
    $stmt->bindParam(":contact_number", $data['contact_number']);
    $stmt->bindParam(":email", $data['email']);
    $stmt->bindParam(":password_hash", $password_hash);
    $stmt->bindParam(":user_type", $data['user_type']);
    $stmt->bindParam(":status", $data['status']);

    // Execute the query
    if ($stmt->execute()) {
        sendJsonResponse(["message" => "User registered successfully"], 201);
    } else {
        sendJsonResponse(["error" => "Unable to register user"], 500);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(["error" => "A database error occurred: " . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(["error" => "An unexpected error occurred: " . $e->getMessage()], 500);
}
