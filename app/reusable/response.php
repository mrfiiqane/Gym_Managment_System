<?php

// 1. Function-ka Response-ka
if (!function_exists('sendResponse')) {
    function sendResponse($status, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }
}

?>