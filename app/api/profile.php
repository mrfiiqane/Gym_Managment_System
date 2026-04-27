<?php
require_once  '../config/init.php';session_start();
header("Content-Type: application/json");
include '../config/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Unauthorized Access"]);
    exit;
}

$action = $_POST['action'] ?? "";
$user_id = $_SESSION['user_id'];

function sendResponse($status, $message, $data = null)
{
  echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
  exit;
}

function update_profile($conn, $user_id)
{
    // 1. Validate inputs
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['full_name'])) {
        sendResponse(false, "Username, Email, and Full Name are required.");
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
    
    // 2. Check uniqueness (exclude current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->bind_param("sss", $username, $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        sendResponse(false, "Username or Email already taken by another user.");
    }

    // 3. Handle Password Update (if provided)
    $password_sql = "";
    $types = "ssss";
    $params = [$full_name, $username, $email, $phone];

    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            sendResponse(false, "Passwords do not match.");
        }
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $password_sql = ", password = ?";
        $types .= "s";
        $params[] = $password_hash;
    }

    // 4. Handle Image Upload
    $image_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "user_" . $user_id . "_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_name)) {
            $image_sql = ", image = ?";
            $types .= "s";
            $params[] = $image_name;
            
            // Update Session Image immediately
            $_SESSION['image'] = $image_name;
        } else {
             sendResponse(false, "Failed to upload image.");
        }
    }

    // 5. Update Database
    $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, phone = ? $password_sql $image_sql WHERE id = ?";
    $types .= "s";
    $params[] = $user_id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Update Session Variables
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;

        sendResponse(true, "Profile updated successfully!");
    } else {
        sendResponse(false, "Update failed: " . $stmt->error);
    }
}


/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
  "update_profile"  => "update_profile",
];

if ($action === "" || !isset($allowedActions[$action])) {
  sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

// Nadiifi natiijooyinka SP (Aad u muhiim ah)
while ($conn->next_result()) {
  $conn->store_result();
}
$conn->close();

?>


