<?php
require_once  '../config/init.php';
header("Content-Type: application/json");
include '../config/conn.php';

$action = $_POST['action'] ?? "";

function sendResponse($status, $data) {
    echo json_encode(["status" => $status, "data" => $data]);
    exit;
}

function update_profile_image($conn) {
    if (!isset($_SESSION['id'])) {
        sendResponse(false, "Authentication required.");
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        sendResponse(false, "Invalid image upload.");
    }

    $userId = $_SESSION['id'];
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = $userId . "_" . time() . "." . $ext;
    $targetPath = "../uploads/" . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        // Update DB
        $sql = "UPDATE users SET image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fileName, $userId);
        $stmt->execute();

        // Update Session
        $_SESSION['image'] = $fileName;
        sendResponse(true, $fileName);
    } else {
        sendResponse(false, "Failed to save image.");
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
