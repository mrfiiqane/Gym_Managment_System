<?php
require_once  '../config/init.php';

$action = validate($_POST['action'] ?? "");

// 1. INSERT 
function register_actions($conn)
{
    if (!checkRequired(['name', 'system_action', 'link_id'])) {
        sendResponse(false, "All fields are required");
    }

    $data = [
        'name' => validate($_POST['name']),
        'action' => validate($_POST['system_action']),
        'link_id' => (int)$_POST['link_id']
    ];

    if (create($conn, 'system_actions', $data)) {
        sendResponse(true, "Registered System Action Successfully");
    } else {
        sendResponse(false, "Failed to register system action: " . $conn->error);
    }
}

// 2. UPDATE 
function update_actions($conn)
{
    if (!checkRequired(['id', 'name', 'system_action', 'link_id'])) {
        sendResponse(false, "All fields are required");
    }

    $id = (int)$_POST['id'];
    $data = [
        'name' => validate($_POST['name']),
        'action' => validate($_POST['system_action']),
        'link_id' => (int)$_POST['link_id']
    ];

    if (update($conn, 'system_actions', $data, $id)) {
        sendResponse(true, "Updated System Action Successfully");
    } else {
        sendResponse(false, "Failed to update system action: " . $conn->error);
    }
}

// 3. READ ALL (Using SP)
function read_all_actions($conn)
{
    $search = validate($_POST['p_search'] ?? '');
    $limit  = (int)($_POST['p_limit'] ?? 10);
    $offset = (int)($_POST['p_offset'] ?? 0);

    $data = db_read_all_sp($conn, 'read_all_system_actions_sp', [$search, $limit, $offset]);

    if ($data !== false) {
        sendResponse(true, "System actions retrieved successfully", $data);
    } else {
        sendResponse(false, "Failed to fetch system actions via SP");
    }
}

// 4. READ SINGLE 
function read_info($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    
    $id = (int)$_POST['id'];
    $sql = "SELECT * FROM system_actions WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        sendResponse(true, "System action retrieved", $data);
    } else {
        sendResponse(false, "Failed to fetch system action");
    }
}

// 5. DELETE 
function delete_actions($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    
    $id = (int)$_POST['id'];
    
    if (delete($conn, 'system_actions', $id)) {
        sendResponse(true, "Deleted System Action Successfully");
    } else {
        sendResponse(false, "Failed to delete system action");
    }
}

/* =========================
   ACTION WHITELIST & EXECUTION
========================= */
$allowedActions = [
    "register_actions" => "register_actions",
    "update_actions"   => "update_actions",
    "read_all"         => "read_all_actions",
    "read_info"        => "read_info",
    "delete_actions"   => "delete_actions",
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

$allowedActions[$action]($conn);
$conn->close();

