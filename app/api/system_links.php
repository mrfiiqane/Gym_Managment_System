<?php
require_once  '../config/init.php';
include '../reusable/response.php';
include '../reusable/validator.php';
include '../reusable/db_crud_helper.php';
include '../config/conn.php';

$action = validate($_POST['action'] ?? "");

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
        sendResponse(true, "Links found", $array_data);
    } else {
        sendResponse(false, "No Link Found");
    }
}

// 1. INSERT 
function register_links($conn)
{
    if (!checkRequired(['name', 'link_id', 'category'])) {
        sendResponse(false, "All fields are required");
    }

    $data = [
        'name' => validate($_POST['name']),
        'link' => validate($_POST['link_id']),
        'category_id' => (int)$_POST['category']
    ];

    if (create($conn, 'system_links', $data)) {
        sendResponse(true, "Link Registered Successfully");
    } else {
        sendResponse(false, "Failed to register link");
    }
}

// 2. UPDATE
function update_links($conn)
{
    if (!checkRequired(['id', 'name', 'link_id', 'category'])) {
        sendResponse(false, "All fields are required");
    }

    $id = (int)$_POST['id'];
    $data = [
        'name' => validate($_POST['name']),
        'link' => validate($_POST['link_id']),
        'category_id' => (int)$_POST['category']
    ];

    if (update($conn, 'system_links', $data, $id)) {
        sendResponse(true, "Link Updated Successfully");
    } else {
        sendResponse(false, "Failed to update link");
    }
}

// 3. READ ALL
function read_all($conn)
{
    $search = validate($_POST['p_search'] ?? '');
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
        sendResponse(true, "Links retrieved", $data);
    } else {
        sendResponse(false, "Failed to fetch links");
    }
}

// 4. READ SINGLE
function read_info($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    
    $id = (int)$_POST['id'];
    $sql = "SELECT * FROM system_links WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        sendResponse(true, "Link retrieved", $data);
    } else {
        sendResponse(false, "Failed to fetch link");
    }
}

// 5. DELETE
function delete_links($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    
    $id = (int)$_POST['id'];
    
    if (delete($conn, 'system_links', $id)) {
        sendResponse(true, "Link Deleted Successfully");
    } else {
        sendResponse(false, "Failed to delete link");
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

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

// Fulinta function-ka loo baahanyahay
$allowedActions[$action]($conn);

$conn->close();