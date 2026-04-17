<?php
require_once '../config/init.php';

$action = validate($_POST['action'] ?? "");

// INSERT loop 
function authorize_user($conn)
{
    $user_id = validate($_POST['user_id'] ?? '');
    $action_id = $_POST['action_id'] ?? [];

    if (empty($user_id)) {
        sendResponse(false, "User ID is required");
    }

    $success_count = 0;
    $error_array = [];

    // 1. Tirtir xuquuqdii hore ee user-ka
    $del = $conn->prepare("DELETE FROM user_authority WHERE user_id = ?");
    $del->bind_param("s", $user_id);
    if (!$del->execute()) {
        sendResponse(false, "Failed to delete old permissions", $conn->error);
    }

    // 2. Geli xuquuqaha cusub
    if (is_array($action_id) && count($action_id) > 0) {
        foreach ($action_id as $id) {
            $cleaned_id = validate($id);
            if (create($conn, 'user_authority', ['user_id' => $user_id, 'action' => $cleaned_id])) {
                $success_count++;
            } else {
                $error_array[] = "Link ID $cleaned_id error";
            }
        }
    }

    if ($success_count > 0 && count($error_array) == 0) {
        sendResponse(true, "Si guul ah ayaa loo siiyay xuquuqda.", ["inserted" => $success_count]);
    } elseif ($success_count > 0) {
        sendResponse(true, "Qaar waa la geliyay, qaar kale se waa ku fashilmeen.", ["success" => $success_count, "errors" => $error_array]);
    } else {
        sendResponse(true, "Xuquuqdii hore waa laga qaaday, balse wax cusub lama siin.", ["inserted" => 0]);
    }
}

// READ ALL SYSTEM AUTHORITY VIEWS
function read_all_authorities($conn)
{
    $data = read_all_generic($conn, 'system_authority', "ORDER BY id ASC");
    if ($data !== false) {
        sendResponse(true, "Authorities fetched successfully", $data);
    } else {
        sendResponse(false, "Execute failed");
    }
}

function read_all_generic($conn, $table, $extra_sql = "") {
    $sql = "SELECT * FROM `$table` $extra_sql";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
}

// Using SP: get_user_authorites_sp
function get_user_authorities($conn)
{
    $user_id = validate($_POST['user_id'] ?? '');
    
    if (empty($user_id)) {
        sendResponse(false, "User ID is missing");
    }

    $data = db_read_all_sp($conn, 'get_user_authorites_sp', [$user_id]);

    if ($data !== false) {
        sendResponse(true, "Authorities fetched effectively", $data);
    } else {
        sendResponse(false, "Failed to fetch user authorities via SP");
    }
}

// Using SP: get_user_menu_sp
function get_User_Menus($conn)
{
    $user_id = $_SESSION['user_id'] ?? '';

    if (empty($user_id)) {
        sendResponse(false, "Your session is invalid or expired please login.");
    }

    $data = db_read_all_sp($conn, 'get_user_menu_sp', [$user_id]);

    if ($data !== false) {
        sendResponse(true, "User menus fetched successfully", $data);
    } else {
        sendResponse(false, "Failed to fetch menus via SP");
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "authorize_user" => "authorize_user",
    "get_user_authorities" => "get_user_authorities",
    "read_all" => "read_all_authorities",
    "read_all_generic" => "read_all_generic",
    "read_all_authorities" => "read_all_authorities",
    "get_User_Menus" => "get_User_Menus",
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

$allowedActions[$action]($conn);
$conn->close();