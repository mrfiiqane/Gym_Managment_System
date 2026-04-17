<?php
require_once '../../config/init.php';

$action = $_POST['action'] ?? "";

// // Check unauthorized access
// $publicActions = ['register_user'];
// if (!in_array($action, $publicActions)) {
//     if (!isset($_SESSION['user_id'])) {
//         sendResponse(false, "Unauthorized Access");
//     }
// }

// // CSRF Verification for modifying actions
// $modifyingActions = ['update_user', 'delete_user', 'approve_user', 'update_status'];
// if (in_array($action, $modifyingActions)) {
//     $token = $_POST['csrf_token'] ?? '';
//     if (!verify_csrf_token($token)) {
//         sendResponse(false, "Invalid security token. Please refresh the page.");
//     }
// }

function register_user($conn)
{
    // 1. Hubi in xogta lagama maarmaanka ah ay timid
    if (!checkRequired(['username', 'email', 'role_id'])) {
        sendResponse(false, "incomplete data or invalid email");
    }

    $full_name = validate($_POST['full_name'] ?? '');
    $username = validate($_POST['username']);
    $email = filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL);
    $phone = validate($_POST['phone'] ?? '');
    $role = validate($_POST['role_id']); // Hubi in kani yahay ID (Number)
    $password = !empty($_POST['password']) ? $_POST['password'] : "123456";

    if (empty($full_name) || empty($username) || empty($phone) || empty($email)) {
        sendResponse(false, "please fill all the required fields");
    }

    // 2. Hubi haddii hore loo isticmaalay Username ama Email
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        sendResponse(false, "Username or email already exists");
    }

    // 3. Diyaarinta macluumaadka bilowga ah
    $newid = generate($conn);
    $hashed_password = hash_password($password);
    $status = 'Active';
    $final_image = 'default.png'; // Sawirka haddii uusan qofku soo upload-gareyn mid cusub

    // 4. Maareynta Sawirka (Halkan ka fiiro gaar ah)
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        // Halkan waxaan u dhiibeynaa: File-ka, Folder-ka (USER_UPLOAD_PATH), iyo Horgalaha (USER)
        $uploadResult = upload_image(
            $_FILES['image'],
            USER_UPLOAD_PATH,
            'USER'
        );

        if (!$uploadResult['status']) {
            sendResponse(false, $uploadResult['message']);
        }

        $final_image = $uploadResult['filename'];
    }

    // 5. Kaydinta Database-ka
    // Hubi: 'ssssssiss' (s=string, i=integer). Role_id waa inuu 'i' noqdaa haddii uu number yahay
    $stmt = $conn->prepare("INSERT INTO users (id, full_name, username, email, phone, password, role_id, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $newid, $full_name, $username, $email, $phone, $hashed_password, $role, $status, $final_image);

    if ($stmt->execute()) {
        $user_data = [
            "id" => $newid,
            "full_name" => $full_name,
            "username" => $username,
            "image" => $final_image, // Dib ugu celi magaca sawirka si loogu arko Frontend-ka
            "role" => $role
        ];
        sendResponse(true, "User-ka waa la diiwaangeliyey si guul ah", $user_data);
    } else {
        error_log("Insert failed: " . $conn->error);
        sendResponse(false, "Diiwaangelintu way fashilantay cillad awgeed. Waan ka xunnahay.");
    }
}

 


function read_all_users($conn)
{
    $search = validate($_POST['search'] ?? '');
    $role_filter = validate($_POST['role_id'] ?? '');
    $status_filter = validate($_POST['status'] ?? '');
    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $whereClause = "WHERE 1=1";
    $params = [];
    $types = "";

    if ($search) {
        $whereClause .= " AND (username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
        $types .= "sss";
    }

    if ($role_filter && $role_filter !== 'all') {
        $whereClause .= " AND role_id = ?";
        $params[] = $role_filter;
        $types .= "s";
    }

    if ($status_filter === 'pending') {
        $whereClause .= " AND status = 'pending'";
    }

    // Count Total
    $countSql = "SELECT COUNT(*) as total FROM users $whereClause";
    $stmtCount = $conn->prepare($countSql);
    if (!empty($types)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $total = $stmtCount->get_result()->fetch_assoc()['total'];

    // Get Data
    $sql = "SELECT 
            u.id, 
            u.username, 
            u.full_name, 
            u.email, 
            u.status, 
            u.image, 
            u.phone,
            u.created_at, 
            r.role_name 
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id 
        $whereClause 
        ORDER BY u.id DESC 
        LIMIT ? OFFSET ?";

    // $sql = "SELECT id, username, full_name, email, role_id, status, image, phone, created_at FROM users $whereClause ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    // Halkan fiiro u yeelo: Pagination params
    $finalTypes = $types . "ii";
    $finalParams = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($finalTypes, ...$finalParams);

    $stmt->execute();
    // Diyaari xogta aad rabto inaad dib u celiso
    $raw_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $users_list = [];

    foreach ($raw_users as $user) {
        $users_list[] = [
            "id" => $user['id'],
            "full_name" => $user['full_name'],
            "username" => $user['username'],
            "phone" => $user['phone'],
            "email" => $user['email'],
            // "password" => $user['password'],
            "role_name" => $user['role_name'],
            "status" => $user['status'],
            "image" => $user['image'],
            "created_at" => $user['created_at'],
        ];
    }

    // Stats
    $stats = [
        'total' => $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
        'students' => $conn->query("SELECT COUNT(*) as c FROM users WHERE role_id = 3")->fetch_assoc()['c'],
        'teachers' => $conn->query("SELECT COUNT(*) as c FROM users WHERE role_id = 2")->fetch_assoc()['c'],
        'pending' => $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c']
    ];

    sendResponse(true, "Fetched successfully", ["users" => $users_list, "stats" => $stats, "total" => $total]);
}

function Read_All($conn)
{
    $sql = "SELECT * FROM `users` WHERE status = 'Active' ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, "Authorities fetched successfully", $data);
    } else {
        error_log("Query failed: " . $conn->error);
        sendResponse(false, "A database error occurred.");
    }
}

function read_single_user($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID lama soo dirin.");
    }

    $id = validate($_POST['id']);
    $stmt = $conn->prepare("SELECT id, username, full_name, email, phone, password, role_id, image FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendResponse(true, "User data fetched", $result->fetch_assoc());
    } else {
        sendResponse(false, "User-ka lama helin.");
    }
}

function update_user($conn)
{

    $id = validate($_POST['id']); // USR0007
    $username = validate($_POST['username']);
    $email = filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL);
    $full_name = validate($_POST['full_name']);
    $phone = validate($_POST['phone']);
    $password = $_POST['password'] ?? null;
    $role_id = validate($_POST['role_id'] ?? '0');

    // 1. Hel xogta hadda jirta (Role iyo Sawirka hore)
    $current_stmt = $conn->prepare("SELECT role_id, image FROM users WHERE id = ?");
    $current_stmt->bind_param("s", $id);
    $current_stmt->execute();
    $current_user = $current_stmt->get_result()->fetch_assoc();

    if (!$current_user) {
        sendResponse(false, "User-ka lama helin");
    }

    // 2. XALKA ROLE-KA: Haddii aan la dooran role cusub (0), qaado kii hore
    if ($role_id == "0" || empty($role_id)) {
        $role_id = $current_user['role_id'];
    }

    $hashed_password = hash_password($password);

    // 3. Image Update: Isticmaal ID-ga ($id) sidii prefix
    $final_image = $current_user['image']; // Default waa kii hore

    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        // Halkan $id (USR0007) ayaan u diraynaa si uu sawirku u noqdo USR0007_xxxx.webp
        $uploadResult = upload_image($_FILES['image'], USER_UPLOAD_PATH, $id, $current_user['image']);

        if (!$uploadResult['status']) {
            sendResponse(false, $uploadResult['message']);
        }

        $final_image = $uploadResult['filename'];
    }

    // 4. Update Database
    $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, phone=?, password=?, role_id=?, image=? WHERE id=?");

    // 'ssssiss' -> s=string, i=integer (role_id)
    $stmt->bind_param("sssssiss", $full_name, $username, $email, $phone, $hashed_password, $role_id, $final_image, $id);

    if ($stmt->execute()) {
        sendResponse(true, "User updated successfully", [
            "id" => $id,
            "image_name" => $final_image,
            "email" => $email,
            "password" => $hashed_password,
            "username" => $username,
            "role_id" => $role_id
        ]);
    } else {
        error_log("Update failed: " . $stmt->error);
        sendResponse(false, "Update failed due to a database error.");
    }
}
 
 
 

function delete_user($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID lama soo dirin.");
    }

    $id = validate($_POST['id']);

    if ($id == $_SESSION['user_id']) {
        sendResponse(false, "You cannot delete your own account");
    }

    $query = $conn->prepare("SELECT username, image FROM users WHERE id = ?");
    $query->bind_param("s", $id);
    $query->execute();
    $user = $query->get_result()->fetch_assoc();

    if (!$user) {
        sendResponse(false, "User-ka lama helin.");
    }

    $username = $user['username'];
    $image_name = $user['image'];

    $del_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $del_stmt->bind_param("s", $id);

    if ($del_stmt->execute()) {
        if ($image_name && $image_name != "default.png") {
            $file_path = USER_UPLOAD_PATH . $image_name;
            if (file_exists(filename: $file_path))
                unlink($file_path);
        }
        sendResponse(true, "User deleted successfully", ["id" => $id, "username" => $username]);
    } else {
        error_log("Delete failed: " . $conn->error);
        sendResponse(false, "Tirtirista database-ka ayaa fashilantay cillad awgeed.");
    }
}

function approve_user($conn)
{
    if (!checkRequired(['id'])) {
        sendResponse(false, "ID is required");
    }
    $id = validate($_POST['id']);
    $stmt = $conn->prepare("UPDATE users SET status = 'Active' WHERE id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        sendResponse(true, "User approved successfully");
    } else {
        error_log("Approval failed: " . $stmt->error);
        sendResponse(false, "Approval failed due to db error.");
    }
}

function update_status($conn)
{
    if (!checkRequired(['id', 'status'])) {
        sendResponse(false, "ID and Status are required");
    }

    $id = validate($_POST['id']);
    $status = validate($_POST['status']);

    if ($id == $_SESSION['user_id'] && $status === 'Block') {
        sendResponse(false, "You cannot block your own account");
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("ss", $status, $id);

    if ($stmt->execute()) {
        sendResponse(true, "User status updated to $status");
    } else {
        error_log("Status fail: " . $stmt->error);
        sendResponse(false, "Status update failed unexpectedly.");
    }
}

function generate($conn)
{
    $res = $conn->query("SELECT id FROM users ORDER BY CAST(SUBSTRING(id, 4) AS UNSIGNED) DESC LIMIT 1");
    if ($res && $res->num_rows) {
        $last_id = $res->fetch_assoc()['id'];
        $num = (int)substr($last_id, 3);
        return sprintf("USR%04d", $num + 1);
    }
    return "USR0001";
}

$allowedActions = [
    "register_user" => "register_user",
    "read_all_users" => "read_all_users",
    "Read_All" => "Read_All",
    "read_single_user" => "read_single_user",
    "update_user" => "update_user",
    "delete_user" => "delete_user",
    "approve_user" => "approve_user",
    "update_status" => "update_status",
];

if (!isset($allowedActions[$action])) {
    sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();
?>