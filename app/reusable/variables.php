<?php
// Core Session Variables
$current_role = $_SESSION['role'] ?? '';
$full_name = $_SESSION['full_name'] ?? 'User';
$user_image = $_SESSION['image'] ?? 'default.png';

// Page Routing & Highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$class_pages = ['index.php', 'my_classes.php', 'browse.php']; // Gym Class Pages
$is_class_active = in_array($current_page, $class_pages);
?>
