<?php

require_once '../../config/init.php';
header("Content-Type: application/json");

$action = $_POST['action'] ?? "";

function user_login($conn)
{

  $username = validate($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($username)) {
    sendResponse(false, 'Fill Username or Email is required');
  }
  if (empty($password)) {
    sendResponse(false, 'Password is required');
  }
  $length = strlen($password);
  if ($length < 3) {
    sendResponse(false, "Password must be at least 6 characters");
  }
  if ($length > 20) {
    sendResponse(false, "Password must lower 20 characters");
  }

  try {


    $stmt = $conn->prepare("CALL login_sp(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      sendResponse(false, "Account not found with this username or email.");
    }

    $user = $result->fetch_assoc();
    $stmt->close();


    // Verify Status sida active,block
    if ($user['status'] !== 'Active') {
      sendResponse(false, 'Your account is ' . $user['status'] . '. Please contact Administrator to support.');
    }


    // 2. Verify the password
    if (!verify_password($password, $user['password'])) {
      sendResponse(false, "Incorrect password. Please try again.");
    }

    // Regenerate session ID to prevent fixation
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Set Session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['image'] = $user['image'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['role'] = $user['role_name'];
    $_SESSION['last_login'] = time();

    // Halkan waaye Automatic Login-ka (30 maalmood ayay haysanaysaa Session-ka)
    if (isset($_POST['remember']) && $_POST['remember'] === 'true') {
        setcookie(session_name(), session_id(), time() + (86400 * 30), "/");
    }



    // // Return redirect URL based on role
    // $redirect = "../../views/dashboard.php"; // Default redirect

    sendResponse(true, 'Login successful', [
      'role' => $user['role_name'],
      // 'redirect' => $redirect
    ]);

  } catch (Exception $e) {
    error_log($e->getMessage());
    sendResponse(false, 'A technical error occurred.');
  }
}




$allowedActions = [
  "user_login" => "user_login",
];

if (!isset($allowedActions[$action])) {
  sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();
?>