<?php
require_once '../config/init.php';
header("Content-Type: application/json");
include '../config/conn.php';

$action = $_POST['action'] ?? "";

// Function-ka Response-ka
function sendResponse($status, $message = null, $data = null)
{
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

// Function-ka Sanitization
function cleanInput($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// INSERT loop 
function authorize_user($conn)
{
    $user_id = cleanInput($conn, $_POST['user_id'] ?? '');
    $action_id = $_POST['action_id'] ?? [];

    if (empty($user_id)) {
        sendResponse(false, "User ID is required");
    }

    $success_count = 0;
    $error_array = [];

    // 1. Tirtir xuquuqdii hore ee user-ka (SECURE)
    $del = $conn->prepare("DELETE FROM user_authority WHERE user_id = ?");
    $del->bind_param("s", $user_id);
    if (!$del->execute()) {
        sendResponse(false, "Cillad tirtirid: " . $conn->error);
    }

    // 2. Geli xuquuqaha cusub
    if (is_array($action_id) && count($action_id) > 0) {
        $ins = $conn->prepare("INSERT INTO user_authority (user_id, action) VALUES (?, ?)");
        foreach ($action_id as $id) {
            $cleaned_id = cleanInput($conn, $id);
            $ins->bind_param("ss", $user_id, $cleaned_id);
            if ($ins->execute()) {
                $success_count++;
            } else {
                $error_array[] = "Link ID $cleaned_id error: " . $conn->error;
            }
        }
    }

    // 3. Jawaabta (Response)
    if ($success_count > 0 && count($error_array) == 0) {
        sendResponse(true, "Si guul ah ayaa loo siiyay xuquuqda.", ["inserted" => $success_count]);
    } elseif ($success_count > 0) {
        sendResponse(true, "Qaar waa la geliyay, qaar kale se waa ku fashilmeen.", ["success" => $success_count, "errors" => $error_array]);
    } else {
        // Haddi uusan dooran waxba, laakiin tirtiray wixii hore, waa la keydiyay.
        sendResponse(true, "Xuquuqdii hore waa laga qaaday, balse wax cusub lama siin.", ["inserted" => 0]);
    }
}

// READ ALL SYSTEM AUTHORITY VIEWS
function read_all($conn)
{
    $sql = "SELECT * FROM `system_authority` ORDER BY id ASC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendResponse(false, "Prepare failed: " . $conn->error);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, "Authorities fetched successfully", $data);
    } else {
        sendResponse(false, "Execute failed: " . $stmt->error);
    }
}

function get_user_authorities($conn)
{
    $user_id = cleanInput($conn, $_POST['user_id'] ?? '');
    
    if (empty($user_id)) {
        sendResponse(false, "User ID is missing");
    }

    $sql = "SELECT c.id as category_id, c.name as category_name, c.role as category, 
                   sl.id as link_id, sl.name as link_name, 
                   sa.id as action_id, sa.name as action_name
            FROM user_authority ua
            LEFT JOIN system_actions sa ON ua.action = sa.id
            LEFT JOIN system_links sl ON sa.link_id = sl.id
            LEFT JOIN category c ON sl.category_id = c.id
            WHERE ua.user_id = ? 
            ORDER BY c.role, sl.id, sa.id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, "Authorities fetched effectively", $rows);
    } else {
        sendResponse(false, "Failed to fetch user authorities: " . $conn->error);
    }
}

function get_User_Menus($conn)
{
    // Hubi in Session-ku jiro
    $user_id = $_SESSION['user_id'] ?? '';

    if (empty($user_id)) {
        sendResponse(false, "Your session is invalid or expired please login.");
    }

    $sql = "SELECT c.id as category_id, c.name as category_name, c.role as category, c.icon as category_icon,
                   sl.id as link_id, sl.name as link_name, sl.link
            FROM user_authority ua
            LEFT JOIN system_actions sa ON ua.action = sa.id
            LEFT JOIN system_links sl ON sa.link_id = sl.id
            LEFT JOIN category c ON sl.category_id = c.id
            WHERE ua.user_id = ? 
            GROUP BY sl.id 
            ORDER BY c.name, sl.id, sa.id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $array_data = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, "User menus fetched successfully", $array_data);
    } else {
        sendResponse(false, $conn->error);
    }
}


/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "authorize_user" => "authorize_user",
    "get_user_authorities" => "get_user_authorities",
    "read_all" => "read_all",
    "get_User_Menus" => "get_User_Menus",
];

if ($action === "" || !isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

$conn->close();