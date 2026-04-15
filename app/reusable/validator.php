<?php
function validate($data) {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    // Removed htmlspecialchars() so DB stores raw (safe via prepared statements), enabling easy API/mobile reuse.
    // Ensure you use htmlspecialchars() ON OUTPUT in views!
    return $data;
}

function checkRequired($fields) {
    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

// example 
// if (!checkRequired(['id', 'name', 'icon', 'role'])) {
//         sendResponse(false, "Fadlan buuxi dhamaan meelaha bannaan");
//     }

//     $id = (int)$_POST['id'];
//     $name = validate($_POST['name']);
//     $icon = validate($_POST['icon']);
//     $role = validate($_POST['role']);

?>
