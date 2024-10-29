<?php
// Enable error reporting for debugging
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

// Set headers for CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(["error" => "Method not allowed"], 405);
}

try {
    include_once '../connection/dbconfig.php';

    error_log("dbconfig.php included successfully");

    $data = json_decode(file_get_contents('php://input'), true);

    // Get the posted data
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;


    // Validate input
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required.']);
        exit;
    }

    // Create database connection
    $database = new database();
    $db = $database->getDb();

    // Prepare the SQL query
    $query = "SELECT user_id, first_name, last_name, email, password_hash, user_type, status FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                http_response_code(403);
                echo json_encode(['error' => 'Account is not active']);
                exit;
            }

            // Login successful
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'user_id' => $user['user_id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'user_type' => $user['user_type'],
                'status' => $user['status']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendJsonResponse(["error" => "A database error occurred"], 500);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendJsonResponse(["error" => "An unexpected error occurred"], 500);
}
