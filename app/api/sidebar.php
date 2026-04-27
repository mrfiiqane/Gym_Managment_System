<?php
require_once '../config/init.php';

$action = $_POST['action'] ?? "";

function sendResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

function update_profile_image($conn) {
    // Standard session key in this project is 'user_id'
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "Authentication required.");
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        sendResponse(false, "Invalid image upload.");
    }

    $userId = $_SESSION['user_id'];
    
    // Use the robust upload_image helper from init.php
    // USER_UPLOAD_PATH = app/uploads/User_profile/
    $oldImage = $_SESSION['image'] ?? 'default.png';
    $result = upload_image($_FILES['image'], USER_UPLOAD_PATH, $userId, $oldImage);

    if ($result['status']) {
        $fileName = $result['filename'];
        
        // Update Database
        $sql = "UPDATE users SET image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fileName, $userId);
        
        if ($stmt->execute()) {
            // Update Session
            $_SESSION['image'] = $fileName;
            sendResponse(true, "Profile image updated successfully!", $fileName);
        } else {
            // Optionally delete the uploaded file if DB update fails
            $filePath = USER_UPLOAD_PATH . $fileName;
            if (file_exists($filePath)) unlink($filePath);
            
            sendResponse(false, "Failed to update database: " . $conn->error);
        }
    } else {
        sendResponse(false, $result['message'] ?? "Failed to upload image.");
    }
}

$allowedActions = [
    "update_profile_image" => "update_profile_image"
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid action.");
}

$allowedActions[$action]($conn);
$conn->close();
?>
