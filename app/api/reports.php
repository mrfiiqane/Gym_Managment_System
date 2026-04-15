<?php
session_start();
header("Content-Type: application/json");
include '../config/conn.php';

$action = $_POST['action'] ?? "";

function sendResponse($status, $message, $data = null)
{
  echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
  exit;
}

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'Student') {
    sendResponse(false, "Unauthorized Access");
}

function get_summary_stats($conn) {
    $result = $conn->query("CALL GetSummaryStats()");
    $stats = $result->fetch_assoc();
    $result->free();
    $conn->next_result();
    sendResponse(true, "Summary stats fetched", $stats);
}

function get_attendance_trends($conn) {
    $result = $conn->query("CALL GetAttendanceTrends()");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $conn->next_result();
    sendResponse(true, "Attendance trends fetched", $data);
}

function get_grade_analysis($conn) {
    $result = $conn->query("CALL GetGradeAnalysis()");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $conn->next_result();
    sendResponse(true, "Grade analysis fetched", $data);
}

function get_role_distribution($conn) {
    $result = $conn->query("CALL GetRoleDistribution()");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $conn->next_result();
    sendResponse(true, "Role distribution fetched", $data);
}

$allowedActions = [
    "summary" => "get_summary_stats",
    "attendance_trends" => "get_attendance_trends",
    "grade_analysis" => "get_grade_analysis",
    "role_distribution" => "get_role_distribution"
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();
?>
