<?php
require_once '../../config/init.php';

$action = $_POST['action'] ?? "";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    sendResponse(false, "Unauthorized Access");
}

function get_admin_stats($conn) {
    // 1. Hel Result-ka kowaad (Counts)
    $stmt = $conn->prepare("CALL sp_get_admin_dashboard_stats()");
    $stmt->execute();
    $stats_result = $stmt->get_result();
    $stats = $stats_result ? $stats_result->fetch_assoc() : [];
    if ($stats_result) $stats_result->free();
    
    // 3. Result 2: Recent Enrollments
    $recent = [];
    if ($stmt->next_result()) {
        $result2 = $stmt->get_result();
        if ($result2) {
            $recent = $result2->fetch_all(MYSQLI_ASSOC);
            $result2->free();
        }
    }
    
    // 4. Result 3: Monthly Enrollments Trend
    $trend = [];
    if ($stmt->next_result()) {
        $result3 = $stmt->get_result();
        if ($result3) {
            $trend = $result3->fetch_all(MYSQLI_ASSOC);
            $result3->free();
        }
    }

    // 5. Result 4: Course Level Distribution
    $distribution = [];
    if ($stmt->next_result()) {
        $result4 = $stmt->get_result();
        if ($result4) {
            $distribution = $result4->fetch_all(MYSQLI_ASSOC);
            $result4->free();
        }
    }
    
    // flush any remaining results 
    while($stmt->next_result()) {;}

    sendResponse(true, "Admin stats fetched successfully", [
        'stats'  => $stats,
        'recent' => $recent,
        'trend' => $trend,
        'distribution' => $distribution
    ]);
}


$allowedActions = [
  "get_admin_stats" => "get_admin_stats",
];

if (!isset($allowedActions[$action])) {
  sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();


?>