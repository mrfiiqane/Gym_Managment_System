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

// readall links pages on views folder 
function read_all_system_links($conn)
{
    $array_data = array();

    $search1 = glob('../views/*.html');
    $search2 = glob('../views/*.php');
    $search_result = array_merge($search1, $search2);

    foreach ($search_result as $sr) {
        $pure_link = explode('/', $sr);
        $array_data[] = end($pure_link); // Retrieves the last element safely
    }

    if (count($array_data) > 0) {
        sendResponse(true, $array_data);
    } else {
        sendResponse(false, "No Link Found");
    }
}

// 3. INSERT 
function register_links($conn)
{
    $name = cleanInput($conn, $_POST['name']);
    $link = cleanInput($conn, $_POST['link_id']);
    $category = (int)($_POST['category'] ?? 0);

    if (empty($name) || empty($link) || empty($category)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "INSERT INTO system_links (`name`, `link`, `category_id`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $link, $category);

    if ($stmt->execute()) {
        sendResponse(true, "Link Registered Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

// 4. UPDATE
function update_links($conn)
{
    $id = (int)($_POST['id'] ?? 0);
    $name = cleanInput($conn, $_POST['name']);
    $link = cleanInput($conn, $_POST['link_id']);
    $category = (int)($_POST['category'] ?? 0);

    if (empty($id) || empty($name) || empty($link) || empty($category)) {
        sendResponse(false, "All fields are required");
    }

    $sql = "UPDATE system_links SET `name`=?, `link`=?, `category_id`=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $link, $category, $id);

    if ($stmt->execute()) {
        sendResponse(true, "Link Updated Successfully");
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
    $countSql = "SELECT COUNT(*) as total FROM system_links WHERE name LIKE CONCAT('%', ?, '%')";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $search);
    $countStmt->execute();
    $totalCount = $countStmt->get_result()->fetch_assoc()['total'];

    // Get Data with Join to Category
    $sql = "SELECT sl.*, c.name as category_name, ? as TotalCount 
            FROM system_links sl
            LEFT JOIN category c ON sl.category_id = c.id
            WHERE sl.name LIKE CONCAT('%', ?, '%') 
            ORDER BY sl.id DESC LIMIT ? OFFSET ?";
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
    $sql = "SELECT * FROM system_links WHERE id=?";
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
function delete_links($conn)
{
    $id = (int)($_POST['id'] ?? 0);
    $sql = "DELETE FROM system_links WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        sendResponse(true, "Link Deleted Successfully");
    } else {
        sendResponse(false, $conn->error);
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "register_links"        => "register_links",
    "update_links"          => "update_links",
    "read_all"              => "read_all",
    "read_info"             => "read_info",
    "delete_links"          => "delete_links",
    "read_all_system_links" => "read_all_system_links",
];

if ($action === "" || !isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

$conn->close();