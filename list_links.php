<?php
require_once 'app/config/init.php';
$res = $conn->query("SELECT sl.*, c.name as category_name FROM system_links sl LEFT JOIN category c ON sl.category_id = c.id");
while($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Category: {$row['category_name']} | Name: {$row['name']} | Link: {$row['link']}\n";
}
