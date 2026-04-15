<?php
// App Initialization
date_default_timezone_set('Africa/Mogadishu'); 
// Main Configuration File

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Load Environment Variables (.env) ---
function load_env() {
    $env_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '.env';
    if (file_exists($env_path)) {
        $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}
load_env();

// Global Application Configuration
define('APP_NAME', 'FitCore Gym Management System');
define('APP_VERSION', '1.0.0');
// Dynamically get the base URL to avoid port and domain issues
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . $host . '/Gym_Managment_system/app/');

// Load Required Files Early
require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../reusable/response.php';
require_once __DIR__ . '/../reusable/validator.php';
require_once __DIR__ . '/../reusable/db_crud_helper.php';
require_once __DIR__ . '/../reusable/variables.php';


//  * Check if the current user has access to a specific page
//  * @param string $page_name The filename of the page (e.g., 'student.php')
//  * @return bool True if user has access, redirects to 403/404 if not
//  */
function check_page_access($page_name)
{
    // Skip authentication check for public pages
    $public_pages = [
        'login.php',
        'signup.php',
        'forget_password.php',
        'logout.php',
        'auth/login.php',
        'auth/signup.php',
        'auth/forget_password.php',
        'auth/logout.php'
    ];
    if (in_array($page_name, $public_pages)) {
        return true;
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Admin bypass - Admins have access to all pages
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        return true;
    }

    // Database connection
    global $conn;
    if (!isset($conn)) {
        require_once __DIR__ . '/conn.php';
    }

    // Check if page exists in system_links
    $check_page = $conn->prepare("SELECT id FROM system_links WHERE link = ?");
    $check_page->bind_param("s", $page_name);
    $check_page->execute();
    $page_result = $check_page->get_result();

    if ($page_result->num_rows === 0) {
        // Page not found in database
        http_response_code(404);
        include __DIR__ . '/../views/Errors/404.php';
        exit();
    }

    $page_data = $page_result->fetch_assoc();
    $link_id = $page_data['id'];
    $check_page->close();

    // Check if user has any action permission for this page
    $check_access = $conn->prepare("
        SELECT COUNT(*) as has_access 
        FROM user_authority ua
        INNER JOIN system_actions sa ON ua.action = sa.id
        WHERE ua.user_id = ? AND sa.link_id = ?
    ");
    $check_access->bind_param("si", $user_id, $link_id);
    $check_access->execute();
    $access_result = $check_access->get_result();
    $access_data = $access_result->fetch_assoc();
    $check_access->close();

    if ($access_data['has_access'] > 0) {
        return true;
    } else {
        // User doesn't have access
        http_response_code(403);
        include __DIR__ . '/../views/Errors/403.php';
        exit();
    }


}

/**
 * Check if the current user can perform a specific action
 * @param string $action_name The action name (e.g., 'delete_students')
 * @return bool True if user has permission, false otherwise
 */
function can_access_action($action_name)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];

    // Admin bypass
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        return true;
    }

    global $conn;
    if (!isset($conn)) {
        require_once __DIR__ . '/conn.php';
    }

    $check_action = $conn->prepare("
        SELECT COUNT(*) as has_permission 
        FROM user_authority ua
        INNER JOIN system_actions sa ON ua.action = sa.id
        WHERE ua.user_id = ? AND sa.action = ?
    ");
    $check_action->bind_param("ss", $user_id, $action_name);
    $check_action->execute();
    $result = $check_action->get_result();
    $data = $result->fetch_assoc();
    $check_action->close();

    return $data['has_permission'] > 0;
}

/**
 * Get all allowed actions for the current user
 * @return array Array of action names the user can perform
 */
function get_user_actions()
{
    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $user_id = $_SESSION['user_id'];

    // Admin has all permissions
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        global $conn;
        if (!isset($conn)) {
            require_once __DIR__ . '/conn.php';
        }
        $all_actions = $conn->query("SELECT action FROM system_actions");
        $actions = [];
        while ($row = $all_actions->fetch_assoc()) {
            $actions[] = $row['action'];
        }
        return $actions;
    }

    global $conn;
    if (!isset($conn)) {
        require_once __DIR__ . '/conn.php';
    }

    $get_actions = $conn->prepare("
        SELECT sa.action 
        FROM user_authority ua
        INNER JOIN system_actions sa ON ua.action = sa.id
        WHERE ua.user_id = ?
    ");
    $get_actions->bind_param("s", $user_id);
    $get_actions->execute();
    $result = $get_actions->get_result();

    $actions = [];
    while ($row = $result->fetch_assoc()) {
        $actions[] = $row['action'];
    }
    $get_actions->close();

    return $actions;
}



// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

function is_logged_in(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect(string $path = ''): void
{
    header("Location: " . BASE_URL . $path);
    exit();
}


// qeybta sawirka user Upload & path directory ga

// <img src="">, <a href="">, Wax kasta oo la arkayo frontend
define("USER_UPLOAD_URL", BASE_URL . "uploads/User_profile/");


// Marka aad sawirka SAVE gareyneyso, move_uploaded_file(), unlink(), imagewebp()
// Tani waa hard disk path server path
// . Kan waa Jidka Hard Disk-ga (Physical Path) - Kan ayaa loo isticmaalaa Save-ka
// __DIR__ waxay na siinaysaa halka uu hadda faylka init.php ku yaallo (app/config)
// dirname(__DIR__) waxay dib noogu celinaysaa hal folder (app/)
define("USER_UPLOAD_PATH", dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "User_profile" . DIRECTORY_SEPARATOR);


define("COURSE_UPLOAD_URL", BASE_URL . "uploads/course_thumbnails/");
define("COURSE_UPLOAD_PATH", dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "course_thumbnails" . DIRECTORY_SEPARATOR);

function upload_image(array $file, string $uploadPath, string $prefix = 'IMG', ?string $oldImage = null): array
{
    // 1. Hubi haddii uu jiro upload error
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'Upload error.'];
    }

    // 2. Hubi cabbirka sawirka (8MB)
    if ($file['size'] > 8 * 1024 * 1024) {
        return ['status' => false, 'message' => 'Image must be less than 8MB'];
    }

    $supportedTypes = [
        'image/jpeg' => ['ext' => 'jpg', 'read' => 'imagecreatefromjpeg', 'save' => 'imagejpeg', 'quality' => 85],
        'image/png' => ['ext' => 'png', 'read' => 'imagecreatefrompng', 'save' => 'imagepng', 'quality' => 6],
        'image/webp' => ['ext' => 'webp', 'read' => 'imagecreatefromwebp', 'save' => 'imagewebp', 'quality' => 85]
    ];

    $imageInfo = @getimagesize($file['tmp_name']);
    $mime = $imageInfo['mime'] ?? null;

    if (!$mime || !isset($supportedTypes[$mime])) {
        return ['status' => false, 'message' => 'Allowed: JPG, PNG, WEBP'];
    }

    $typeCfg = $supportedTypes[$mime];

    // 3. Hubi folder-ka, haddii uusan jirinna samee
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    // --- Halkan Ayaa Wax Laga Beddelay (Magaca Sawirka) ---
    // Waxaan isticmaalnaa bin2hex(random_bytes(2)) si uu u soo baxo 4 digit oo kaliya (sida 25e2)
    $shortHash = bin2hex(random_bytes(2));
    $filename = $prefix . '_' . $shortHash . '.' . $typeCfg['ext'];

    // Isticmaal DIRECTORY_SEPARATOR si uu ugu shaqeeyo Windows/XAMPP si sax ah
    $uploadPath = rtrim($uploadPath, DIRECTORY_SEPARATOR);
    $destination = $uploadPath . DIRECTORY_SEPARATOR . $filename;

    $readFunc = $typeCfg['read'];
    if (!function_exists($readFunc)) {
        return ['status' => false, 'message' => 'Server does not support image processing'];
    }

    $source = $readFunc($file['tmp_name']);
    if (!$source) {
        return ['status' => false, 'message' => 'Image processing failed'];
    }

    // 4. Resizing (Cabbirka)
    $width = imagesx($source);
    $height = imagesy($source);
    $ratio = min(500 / $width, 500 / $height, 1);
    $newWidth = (int) ($width * $ratio);
    $newHeight = (int) ($height * $ratio);

    $resized = imagecreatetruecolor($newWidth, $newHeight);

    if ($mime == 'image/png' || $mime == 'image/webp') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
    }

    imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $saveFunc = $typeCfg['save'];
    $saved = $saveFunc($resized, $destination, $typeCfg['quality']);

    imagedestroy($source);
    imagedestroy($resized);

    if (!$saved) {
        return ['status' => false, 'message' => 'Failed to save image'];
    }

    // 5. Tirtir sawirkii hore (Haddii uusan ahayn kii default-ka)
    if ($oldImage && $oldImage !== 'default.png') {
        $oldPath = $uploadPath . DIRECTORY_SEPARATOR . basename($oldImage);
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    return [
        'status' => true,
        'filename' => $filename
    ];
}



// ka hortagaysaa in page-ka lagu kaydiyo cache.
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

// ─── PHPMailer — OTP Email Sender ────────────────────────────────────────────
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

/**
 * Send OTP verification code to user's Gmail
 * @return array ['status' => bool, 'message' => string]
 */
function send_otp_email(string $toEmail, string $toName, string $otp): array
{
    // Use fully qualified class names (no 'use' needed)
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USER'] ?? '';
        $mail->Password   = $_ENV['MAIL_PASS'] ?? '';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 587);
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'FitCore Gym';
        $mail->setFrom($mail->Username, $fromName);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your FitCore Password Reset Code: $otp";
        $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; background: #f8fafc; padding: 30px; border-radius: 16px;'>
            <div style='text-align: center; margin-bottom: 24px;'>
                <div style='display: inline-block; background: linear-gradient(135deg, #3b82f6, #6366f1); padding: 16px; border-radius: 16px;'>
                    <span style='font-size: 28px;'>&#127947;</span>
                </div>
                <h1 style='color: #1e293b; margin-top: 16px; font-size: 22px;'>FitCore Gym</h1>
            </div>
            <div style='background: white; border-radius: 12px; padding: 28px; border: 1px solid #e2e8f0;'>
                <p style='color: #475569; font-size: 15px; margin-bottom: 8px;'>Hi <strong>$toName</strong>,</p>
                <p style='color: #475569; font-size: 14px; line-height: 1.6;'>Your password reset verification code is:</p>
                <div style='text-align: center; margin: 24px 0;'>
                    <div style='display: inline-block; background: linear-gradient(135deg, #eff6ff, #eef2ff); border: 2px dashed #3b82f6; border-radius: 12px; padding: 18px 36px;'>
                        <span style='font-size: 36px; font-weight: 900; letter-spacing: 10px; color: #1d4ed8;'>$otp</span>
                    </div>
                </div>
                <p style='color: #94a3b8; font-size: 13px; text-align: center;'>This code expires in <strong>5 minutes</strong>.</p>
                <hr style='border: none; border-top: 1px solid #f1f5f9; margin: 20px 0;'>
                <p style='color: #cbd5e1; font-size: 12px; text-align: center;'>If you did not request this, please ignore this email.</p>
            </div>
            <p style='color: #cbd5e1; font-size: 11px; text-align: center; margin-top: 16px;'>&copy; " . date('Y') . " FitCore Gym Management System</p>
        </div>";
        $mail->AltBody = "Your FitCore password reset code is: $otp (Expires in 5 minutes)";

        $mail->send();
        return ['status' => true, 'message' => 'Email sent successfully'];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return ['status' => false, 'message' => $mail->ErrorInfo];
    }
}

