<?php
require_once '../config/init.php';
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
function register_category($conn)
{
    $name = cleanInput($conn, $_POST['name']);
    $icon = cleanInput($conn, $_POST['icon']);
    $role = cleanInput($conn, $_POST['role']);

    if (empty($name) || empty($icon) || empty($role)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "INSERT INTO category (`name`, `icon`, `role`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $icon, $role);

    if ($stmt->execute()) {
        sendResponse(true, "Category Registered Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

// 4. UPDATE
function update_category($conn)
{
    $id   = (int)$_POST['id'];
    $name = cleanInput($conn, $_POST['name']);
    $icon = cleanInput($conn, $_POST['icon']);
    $role = cleanInput($conn, $_POST['role']);

    if (empty($id) || empty($name) || empty($icon) || empty($role)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "UPDATE category SET `name`=?, `icon`=?, `role`=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $icon, $role, $id);

    if ($stmt->execute()) {
        sendResponse(true, "Category Updated Successfully");
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
    $countSql = "SELECT COUNT(*) as total FROM category WHERE name LIKE CONCAT('%', ?, '%')";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $search);
    $countStmt->execute();
    $totalCount = $countStmt->get_result()->fetch_assoc()['total'];

    // Get Data
    $sql = "SELECT *, ? as TotalCount FROM category WHERE name LIKE CONCAT('%', ?, '%') ORDER BY id DESC LIMIT ? OFFSET ?";
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
    $id = (int)$_POST['id'];
    $sql = "SELECT * FROM category WHERE id=?";
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
function delete_category($conn)
{
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM category WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        sendResponse(true, "Category Deleted Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "register_category"  => "register_category",
    "update_category"    => "update_category",
    "read_all"           => "read_all",
    "read_info"          => "read_info",
    "delete_category"    => "delete_category",
];

if ($action === "" || !isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

$conn->close();