<?php
require_once '../../config/init.php';
header("Content-Type: application/json");

$action = $_POST['action'] ?? "";

function register_user($conn)
{
  // 1. Validation & Sanitization
  $full_name = validate($_POST['full_name'] ?? '');
  $username  = validate($_POST['username']  ?? '');
  $email     = validate($_POST['email']     ?? '');
  $phone     = validate($_POST['phone']     ?? '');
  $password  = $_POST['password'] ?? '';
  // DB only has role_id 1 (Admin) & 2 (User). Signup always creates a User (2).
  $role_id   = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 2;

  // Block assigning Admin role via public signup
  if ($role_id == 1) {
    sendResponse(false, "Error: Ma isku diiwaangelin kartid Admin ahaan.");
    exit();
  }

  // Force any invalid role to User
  if ($role_id !== 2) {
    $role_id = 2;
  }

  // Required field check
  if (empty($full_name) || empty($username) || empty($phone) || empty($email) || empty($password)) {
    sendResponse(false, "Fadlan buuxi dhammaan meelaha bannaan.");
    exit();
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, "Fadlan geli email sax ah.");
    exit();
  }

  if (strlen($password) < 6 || strlen($password) > 20) {
    sendResponse(false, "Password-ku waa inuu u dhexeeyaa 6 ilaa 20 xaraf.");
    exit();
  }

  // 2. Duplicate Check
  $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
  $checkStmt->bind_param("ss", $username, $email);
  $checkStmt->execute();
  if ($checkStmt->get_result()->num_rows > 0) {
    sendResponse(false, "Username ama Email hore ayaa loo isticmaalay.");
    exit();
  }
  $checkStmt->close();

  // 3. Generate ID & Hash Password
  $new_user_id    = generate($conn);
  $hashed_password = hash_password($password);

  // 4. Secure Image Upload
  $image_name = "default.png";
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload = upload_image($_FILES['image'], USER_UPLOAD_PATH, $new_user_id);
    if ($upload['status']) {
      $image_name = $upload['filename'];
    } else {
      sendResponse(false, "Sawirka: " . $upload['message']);
      exit();
    }
  }

  // 5. Transaction
  $conn->begin_transaction();
  try {
    // 5.1 Call SP to create Pending user
    $stmt = $conn->prepare("CALL singup_sp(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $new_user_id, $full_name, $username, $email, $phone, $hashed_password, $image_name, $role_id);

    if ($stmt->execute()) {
      $stmt->close();

      // 5.2 Generate OTP (6-digit)
      $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
      $expiresAt = date('Y-m-d H:i:s', time() + 300); // 5 minutes

      // 5.3 Store OTP
      $otpStmt = $conn->prepare("INSERT INTO password_resets (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
      $otpStmt->bind_param("sss", $new_user_id, $otp, $expiresAt);
      
      if ($otpStmt->execute()) {
        $conn->commit();
        $otpStmt->close();

        // 5.4 Send Email
        $mailStatus = send_otp_email($email, $full_name, $otp);

        sendResponse(true, "Registration successful! Please check your email for the verification code.", [
          "user_id" => $new_user_id,
          "email"   => $email,
          "mail_sent" => $mailStatus['status']
        ]);
      } else {
        $conn->rollback();
        sendResponse(false, "Failed to generate verification code.");
      }
    } else {
      $conn->rollback();
      sendResponse(false, "Registration Failed: " . $stmt->error);
    }
  } catch (Exception $e) {
    $conn->rollback();
    sendResponse(false, "Server Error: " . $e->getMessage());
  }
}

function verify_otp($conn)
{
  $user_id = validate($_POST['user_id'] ?? '');
  $otp     = validate($_POST['otp']     ?? '');

  if (empty($user_id) || empty($otp)) {
    sendResponse(false, "Verification data missing.");
  }

  // Check OTP — fetch the most recent one for this user
  $stmt = $conn->prepare("SELECT id, expires_at FROM password_resets WHERE user_id = ? AND otp_code = ? AND used = 0 ORDER BY created_at DESC LIMIT 1");
  $stmt->bind_param("ss", $user_id, $otp);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 0) {
    sendResponse(false, "Invalid verification code.");
  }

  $otpRow = $res->fetch_assoc();

  // Check expiry in PHP to avoid DB clock mismatch
  if (strtotime($otpRow['expires_at']) < time()) {
    sendResponse(false, "Verification code has expired. Please resend a new one.");
  }
  $stmt->close();

  // Mark OTP as used and Activate User
  $conn->begin_transaction();
  try {
    $mark = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $mark->bind_param("i", $otpRow['id']);
    $mark->execute();
    $mark->close();

    $activate = $conn->prepare("UPDATE users SET status = 'Active' WHERE id = ?");
    $activate->bind_param("s", $user_id);
    $activate->execute();
    $activate->close();

    $conn->commit();
    sendResponse(true, "Account verified successfully! You can now log in.");
  } catch (Exception $e) {
    $conn->rollback();
    sendResponse(false, "Verification failed: " . $e->getMessage());
  }
}

function resend_otp($conn)
{
  $user_id = validate($_POST['user_id'] ?? '');

  if (empty($user_id)) {
    sendResponse(false, "User identification missing.");
  }

  // Get user email
  $stmt = $conn->prepare("SELECT email, full_name, status FROM users WHERE id = ? LIMIT 1");
  $stmt->bind_param("s", $user_id);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 0) {
    sendResponse(false, "User not found.");
  }

  $user = $res->fetch_assoc();
  $stmt->close();

  if ($user['status'] === 'Active') {
    sendResponse(false, "Account is already verified.");
  }

  // Generate new OTP
  $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
  $expiresAt = date('Y-m-d H:i:s', time() + 300);

  $ins = $conn->prepare("INSERT INTO password_resets (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
  $ins->bind_param("sss", $user_id, $otp, $expiresAt);

  if ($ins->execute()) {
    $ins->close();
    $mailStatus = send_otp_email($user['email'], $user['full_name'], $otp);
    sendResponse(true, "A new code has been sent to your email.", ["mail_sent" => $mailStatus['status']]);
  } else {
    sendResponse(false, "Failed to resend code.");
  }
}


function generate($conn)
{
  $res = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
  if ($res && $res->num_rows) {
    $last_id = $res->fetch_assoc()['id'];
    // Increment alphanumeric ID: USR0001 → USR0002
    $num = (int) substr($last_id, 3) + 1;
    return "USR" . str_pad($num, 4, "0", STR_PAD_LEFT);
  }
  return "USR0001";
}


$allowedActions = [
  "register_user" => "register_user",
  "verify_otp"    => "verify_otp",
  "resend_otp"    => "resend_otp",
];

if (!isset($allowedActions[$action])) {
  sendResponse(false, "Invalid Action");
}

$allowedActions[$action]($conn);
$conn->close();
