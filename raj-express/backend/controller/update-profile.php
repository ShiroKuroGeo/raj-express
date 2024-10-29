<?php
header("Access-Control-Allow-Origin: http://localhost:9000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

include_once '../connection/dbconfig.php';

$database = new Database();
$db = $database->getDb();

// Assume you have a way to get the current user's ID (e.g., from a session)
$user_id = 1; // Replace this with actual user authentication

$response = [];

// Handle form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$current_password = $_POST['currentPassword'] ?? '';
$new_password = $_POST['newPassword'] ?? '';
$confirm_password = $_POST['confirmPassword'] ?? '';

// Split name into first_name and last_name
$name_parts = explode(' ', $name, 2);
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : '';

// Update basic information
$query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, contact_number = :phone, address = :address, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(":first_name", $first_name);
$stmt->bindParam(":last_name", $last_name);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":phone", $phone);
$stmt->bindParam(":address", $address);
$stmt->bindParam(":user_id", $user_id);

if ($stmt->execute()) {
    $response['basic_info'] = "Basic information updated successfully";
} else {
    $response['basic_info'] = "Failed to update basic information";
}

// Handle password change if provided
if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
    if ($new_password === $confirm_password) {
        // Verify current password
        $verify_query = "SELECT password_hash FROM users WHERE user_id = :user_id";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->bindParam(":user_id", $user_id);
        $verify_stmt->execute();
        $user = $verify_stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password_hash'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE users SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
            $update_password_stmt = $db->prepare($update_password_query);
            $update_password_stmt->bindParam(":password_hash", $hashed_password);
            $update_password_stmt->bindParam(":user_id", $user_id);

            if ($update_password_stmt->execute()) {
                $response['password'] = "Password updated successfully";
            } else {
                $response['password'] = "Failed to update password";
            }
        } else {
            $response['password'] = "Current password is incorrect";
        }
    } else {
        $response['password'] = "New password and confirm password do not match";
    }
}

// Handle profile image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/';
    $file_name = uniqid() . '_' . $_FILES['image']['name'];
    $upload_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $image_query = "UPDATE users SET image = :image, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
        $image_stmt = $db->prepare($image_query);
        $image_stmt->bindParam(":image", $file_name);
        $image_stmt->bindParam(":user_id", $user_id);

        if ($image_stmt->execute()) {
            $response['image'] = "Profile image updated successfully";
        } else {
            $response['image'] = "Failed to update profile image in database";
        }
    } else {
        $response['image'] = "Failed to upload profile image";
    }
}

echo json_encode($response);
