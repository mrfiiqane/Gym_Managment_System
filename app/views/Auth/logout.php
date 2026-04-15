<?php
require_once  '../../config/init.php';
session_unset();
session_destroy();
header("Location: " . BASE_URL . "views/Frontend/index.html");
// redirect('views/Frontend/index.html');
exit();
?>





