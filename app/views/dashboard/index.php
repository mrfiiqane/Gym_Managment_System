<?php
require_once '../../config/init.php';

if (!is_logged_in()) {
    redirect('views/Auth/login.php');
    exit;
}

$role = $_SESSION['role'];

// Route to role-specific dashboard
switch ($role) {
    case 'Admin':
        header("Location: admin/index.php");
        break;
    case 'Trainer':
        header("Location: trainer/index.php");
        break;
    case 'Member':
        header("Location: member/index.php");
        break;
    default:
        include '../../reusable/header.php';
        include '../../reusable/sidebar.php';
        echo '<div class="p-10 text-center text-gray-500 font-bold">Unrecognized role. Please contact support.</div>';
        include '../../reusable/footer.php';
        break;
}
exit;
