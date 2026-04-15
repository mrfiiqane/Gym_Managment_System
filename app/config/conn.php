<?php

$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASS'];
$db_name = $_ENV['DB_NAME'];

// Database Connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Handle errors securely
if($conn->connect_error){
    error_log("Database connection failed: " . $conn->connect_error);
    die("A database connection error occurred. Please try again later.");
}
