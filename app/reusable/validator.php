<?php
if (!function_exists('validate')) {
    function validate($data) {
        if ($data === null) return '';
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }
}

if (!function_exists('checkRequired')) {
    function checkRequired($fields) {
        foreach ($fields as $field) {
            if (!isset($_POST[$field]) || $_POST[$field] === '') {
                return false;
            }
        }
        return true;
    }
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
