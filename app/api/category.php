<?php
require_once '../config/init.php';

$action = validate($_POST['action'] ?? "");

// 1. INSERT 
function register_category($conn)
{
    if (!checkRequired(['name', 'icon', 'role'])) {
        sendResponse(false, "All fields are required");
    }

    $data = [
        'name' => validate($_POST['name']),
        'icon' => validate($_POST['icon']),
        'role' => validate($_POST['role'])
    ];

    if (create($conn, 'category', $data)) {
        sendResponse(true, "Category Registered Successfully");
    } else {
        // Log error and send it to identify rejection cause
        sendResponse(false, "Failed to register category: " . $conn->error);
    }
}

// 2. UPDATE
function update_category($conn)
{
    if (!checkRequired(['id', 'name', 'icon', 'role'])) {
        sendResponse(false, "All fields are required");
    }

    $id = (int)$_POST['id'];
    $data = [
        'name' => validate($_POST['name']),
        'icon' => validate($_POST['icon']),
        'role' => validate($_POST['role'])
    ];

    if (update($conn, 'category', $data, $id)) {
        sendResponse(true, "Category Updated Successfully");
    } else {
        sendResponse(false, "Failed to update category: " . $conn->error);
    }
}

// 3. READ ALL (Using SP)
function read_all_categories($conn)
{
    $search = validate($_POST['p_search'] ?? '');
    $limit  = (int)($_POST['p_limit'] ?? 10);
    $offset = (int)($_POST['p_offset'] ?? 0);

    $data = db_read_all_sp($conn, 'read_all_categories_sp', [$search, $limit, $offset]);

    if ($data !== false) {
        sendResponse(true, "Categories retrieved successfully", $data);
    } else {
        sendResponse(false, "Failed to fetch categories via SP");
    }
}

// 4. READ SINGLE 
function read_info($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    
    $id = (int)$_POST['id'];
    $sql = "SELECT * FROM category WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        sendResponse(true, "Category retrieved", $data);
    } else {
        sendResponse(false, "Failed to fetch category");
    }
}

// 5. DELETE
function delete_category($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }

    $id = (int)$_POST['id'];
    
    if (delete($conn, 'category', $id)) {
        sendResponse(true, "Category Deleted Successfully");
    } else {
        sendResponse(false, "Failed to delete category");
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "register_category"  => "register_category",
    "update_category"    => "update_category",
    "read_all"           => "read_all_categories",
    "read_info"          => "read_info",
    "delete_category"    => "delete_category",
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

$allowedActions[$action]($conn);
$conn->close();

