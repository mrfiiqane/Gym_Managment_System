<?php
require_once '../../config/init.php';
header("Content-Type: application/json");

$action = $_POST['action'] ?? "";

function cleanInput($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// ── Action: Send OTP to Gmail ─────────────────────────────────────────────────
function send_otp($conn)
{
    $email = cleanInput($conn, $_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Please enter a valid email address.");
    }

    // Find user
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse(false, "No account found with this email address.");
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Generate 6-digit OTP
    $otp       = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + 300); // 5 minutes

    // Delete old OTPs for this user
    $del = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $del->bind_param("s", $user['id']);
    $del->execute();
    $del->close();

    // Save new OTP
    $ins = $conn->prepare("INSERT INTO password_resets (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
    $ins->bind_param("sss", $user['id'], $otp, $expiresAt);
    $ins->execute();
    $ins->close();

    // Send email via PHPMailer
    $sent = send_otp_email($email, $user['full_name'], $otp);

    if ($sent['status']) {
        sendResponse(true, "A 6-digit code was sent to your email. It expires in 5 minutes.", ['email' => $email]);
    } else {
        sendResponse(false, "Failed to send email: " . $sent['message']);
    }
}

// ── Action: Verify OTP ────────────────────────────────────────────────────────
function verify_otp($conn)
{
    $email = cleanInput($conn, $_POST['email'] ?? '');
    $otp   = cleanInput($conn, $_POST['otp']   ?? '');

    if (empty($email) || empty($otp)) {
        sendResponse(false, "Email and verification code are required.");
    }

    // Get user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        sendResponse(false, "Invalid request.");
    }
    $user = $res->fetch_assoc();
    $stmt->close();

    // Verify OTP — fetch the most recent one for this user
    $check = $conn->prepare("SELECT id, expires_at FROM password_resets WHERE user_id = ? AND otp_code = ? AND used = 0 ORDER BY created_at DESC LIMIT 1");
    $check->bind_param("ss", $user['id'], $otp);
    $check->execute();
    $otpRes = $check->get_result();

    if ($otpRes->num_rows === 0) {
        sendResponse(false, "Invalid code. Please try again.");
    }

    $otpRow = $otpRes->fetch_assoc();

    // Check expiry in PHP to avoid DB clock mismatch
    if (strtotime($otpRow['expires_at']) < time()) {
        sendResponse(false, "Code has expired. Please request a new one.");
    }
    $check->close();

    // Mark OTP as used
    $mark = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $mark->bind_param("i", $otpRow['id']);
    $mark->execute();
    $mark->close();

    sendResponse(true, "Code verified!", ['id' => $user['id']]);
}

// ── Action: Verify Email (legacy, kept for compatibility) ─────────────────────
function verify_email($conn)
{
    $email = cleanInput($conn, $_POST['email']);

    $sql  = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        sendResponse(true, "Email verified.", ["id" => $user['id']]);
    } else {
        sendResponse(false, "No account found with this email.");
    }
}

// ── Action: Reset Password ────────────────────────────────────────────────────
function reset_password($conn)
{
    $id       = cleanInput($conn, $_POST['id']);
    $password = hash_password($_POST['password']);

    $sql  = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $password, $id);

    if ($stmt->execute()) {
        sendResponse(true, "Password reset successfully!");
    } else {
        sendResponse(false, "Failed to reset password.");
    }
}

// ── Router ────────────────────────────────────────────────────────────────────
$allowedActions = [
    "send_otp"      => "send_otp",
    "verify_otp"    => "verify_otp",
    "verify_email"  => "verify_email",
    "reset_password" => "reset_password",
];

if ($action === "" || !isset($allowedActions[$action])) {
    sendResponse(false, "Invalid or Missing Action");
}

$allowedActions[$action]($conn);
$conn->close();
