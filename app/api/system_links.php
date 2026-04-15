<?php
require_once '../config/init.php';

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
        $array_data[] = end($pure_link);
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
        sendResponse(false, "Failed to register link: " . $conn->error);
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
        sendResponse(false, "Failed to update link: " . $conn->error);
    }
}

// 3. READ ALL (Using SP)
function read_all_links($conn)
{
    $search = validate($_POST['p_search'] ?? '');
    $limit  = (int)($_POST['p_limit'] ?? 10);
    $offset = (int)($_POST['p_offset'] ?? 0);

    $data = db_read_all_sp($conn, 'read_all_links_sp', [$search, $limit, $offset]);

    if ($data !== false) {
        sendResponse(true, "Links retrieved successfully", $data);
    } else {
        sendResponse(false, "Failed to fetch links via SP");
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
    "read_all"              => "read_all_links",
    "read_info"             => "read_info",
    "delete_links"          => "delete_links",
    "read_all_system_links" => "read_all_system_links",
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

$allowedActions[$action]($conn);
$conn->close();