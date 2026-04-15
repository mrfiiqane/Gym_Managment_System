<?php
require_once  '../config/init.php';
header("Content-Type: application/json");
include '../config/conn.php';

$action = $_POST['action'] ?? "";

// 1. Function-ka Response-ka
function sendResponse($status, $data)
{
    echo json_encode(["status" => $status, "data" => $data]);
    exit;
}

// 2. Function-ka Sanitization
function cleanInput($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// 3. INSERT 
function register_actions($conn)
{
    $name = cleanInput($conn, $_POST['name']);
    $system_action = cleanInput($conn, $_POST['system_action']);
    $link_id = (int)($_POST['link_id'] ?? 0);

    if (empty($name) || empty($system_action) || empty($link_id)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "INSERT INTO system_actions (`name`, `action`, `link_id`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $system_action, $link_id);

    if ($stmt->execute()) {
        sendResponse(true, "Registered System Action Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

// 4. UPDATE 
function update_actions($conn)
{
    $id = (int)($_POST['id'] ?? 0);
    $name = cleanInput($conn, $_POST['name']);
    $system_action = cleanInput($conn, $_POST['system_action']);
    $link_id = (int)($_POST['link_id'] ?? 0);

    if (empty($id) || empty($name) || empty($system_action) || empty($link_id)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "UPDATE system_actions SET `name`=?, `action`=?, `link_id`=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $system_action, $link_id, $id);

    if ($stmt->execute()) {
        sendResponse(true, "Updated System Action Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

// 5. READ ALL 
function read_all($conn)
{
    $search = cleanInput($conn, $_POST['p_search'] ?? '');
    $limit  = (int)($_POST['p_limit'] ?? 10);
    $offset = (int)($_POST['p_offset'] ?? 0);

    // Get Total Count
    $countSql = "SELECT COUNT(*) as total FROM system_actions WHERE name LIKE CONCAT('%', ?, '%')";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $search);
    $countStmt->execute();
    $totalCount = $countStmt->get_result()->fetch_assoc()['total'];

    // Get Data with Join to Link
    $sql = "SELECT sa.*, sl.name as link_name, ? as TotalCount 
            FROM system_actions sa
            LEFT JOIN system_links sl ON sa.link_id = sl.id
            WHERE sa.name LIKE CONCAT('%', ?, '%') 
            ORDER BY sa.id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $totalCount, $search, $limit, $offset);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, $data);
    } else {
        sendResponse(false, $conn->error);
    }
}

// 6. READ SINGLE 
function read_info($conn)
{
    $id = (int)($_POST['id'] ?? 0);
    $sql = "SELECT * FROM system_actions WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        sendResponse(true, $data);
    } else {
        sendResponse(false, $conn->error);
    }
}

// 7. DELETE 
function delete_actions($conn)
{
    $id = (int)($_POST['id'] ?? 0);
    $sql = "DELETE FROM system_actions WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        sendResponse(true, "Deleted System Action Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "register_actions" => "register_actions",
    "update_actions"   => "update_actions",
    "read_all"         => "read_all",
    "read_info"        => "read_info",
    "delete_actions"   => "delete_actions",
];

if ($action === "" || !isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

$conn->close();
