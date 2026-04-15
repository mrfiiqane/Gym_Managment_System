<?php
require_once './app/config/init.php';

if (is_logged_in()) {
    redirect("views/dashboard/index.php");
} else {
    redirect("views/Frontend/index.html");
}
exit();
