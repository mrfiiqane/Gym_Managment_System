<?php
require_once  '../config/init.php';
header("Content-Type: application/json");
include '../config/conn.php';

$action = $_POST['action'] ?? "";

function sendResponse($status, $message, $data = null)
{
  echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
  exit;
}

function cleanInput($conn, $data)
{
  return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

if (!isset($_SESSION['user_id'])) {
    sendResponse(false, "Unauthorized Access");
}

function read_all_notices($conn)
{
    $sql = "SELECT n.*, u.full_name as author FROM notices n 
            JOIN users u ON n.posted_by = u.id 
            ORDER BY n.created_at DESC";
    $result = $conn->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    sendResponse(true, "Notices fetched", $data);
}

function create_notice($conn)
{
    // Permission check: Admin or Teacher only
    $role = $_SESSION['role'] ?? "";
    if ($role !== 'Admin' && $role !== 'Teacher') {
        sendResponse(false, "Only Admins and Teachers can post notices.");
    }

    if (empty($_POST['title']) || empty($_POST['content'])) {
        sendResponse(false, "Title and Content are required.");
    }

    $title = cleanInput($conn, $_POST['title']);
    $content = cleanInput($conn, $_POST['content']);
    $category = cleanInput($conn, $_POST['category'] ?? "Announcement");
    $expires_at = !empty($_POST['expires_at']) ? cleanInput($conn, $_POST['expires_at']) : null;
    $posted_by = $_SESSION['user_id'];
    $posted_role = $role;

    $stmt = $conn->prepare("INSERT INTO notices (title, content, category, expires_at, posted_by, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $content, $category, $expires_at, $posted_by, $posted_role);

    if ($stmt->execute()) {
        sendResponse(true, "Notice posted successfully");
    } else {
        sendResponse(false, "Failed to post notice: " . $stmt->error);
    }
}

function delete_notice($conn)
{
    // Permission check: Admin or the author
    $notice_id = cleanInput($conn, $_POST['id']);
    $role = $_SESSION['role'] ?? "";
    
    // Check if user is admin OR the author
    $check = $conn->prepare("SELECT posted_by FROM notices WHERE id = ?");
    $check->bind_param("i", $notice_id);
    $check->execute();
    $res = $check->get_result();
    
    if ($res->num_rows === 0) {
        sendResponse(false, "Notice not found.");
    }
    
    $notice = $res->fetch_assoc();
    if ($role !== 'Admin' && $notice['posted_by'] != $_SESSION['user_id']) {
        sendResponse(false, "You do not have permission to delete this notice.");
    }

    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param("i", $notice_id);

    if ($stmt->execute()) {
        sendResponse(true, "Notice deleted successfully");
    } else {
        sendResponse(false, "Delete failed: " . $stmt->error);
    }
}

$allowedActions = [
    "read_all" => "read_all_notices",
    "create" => "create_notice",
    "delete" => "delete_notice"
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();
?>
