<?php
require_once '../../config/init.php';

// ─── Google OAuth Callback Handler ───────────────────────────────────────────
$clientId     = $_ENV['GOOGLE_CLIENT_ID']     ?? '';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
$redirectUri  = $_ENV['GOOGLE_REDIRECT_URI']  ?? '';

$code  = $_GET['code']  ?? '';
$error = $_GET['error'] ?? '';

if ($error || empty($code)) {
    redirect('views/Auth/login.php');
}

// ── Step 1: Exchange code for access token ────────────────────────────────────
$tokenPayload = http_build_query([
    'code'          => $code,
    'client_id'     => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri'  => $redirectUri,
    'grant_type'    => 'authorization_code',
]);

$tokenCtx = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($tokenPayload) . "\r\n",
        'content' => $tokenPayload,
        'timeout' => 15,
    ]
]);

$tokenRaw  = @file_get_contents('https://oauth2.googleapis.com/token', false, $tokenCtx);
$tokenJson = $tokenRaw ? json_decode($tokenRaw, true) : null;

if (!isset($tokenJson['access_token'])) {
    redirect('views/Auth/login.php?error=token_failed');
}

// ── Step 2: Fetch Google user info ────────────────────────────────────────────
$userCtx = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$tokenJson['access_token']}\r\n",
        'timeout' => 10,
    ]
]);

$userRaw  = @file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, $userCtx);
$gUser    = $userRaw ? json_decode($userRaw, true) : null;

if (!isset($gUser['email'])) {
    redirect('views/Auth/login.php?error=userinfo_failed');
}

$googleId = $gUser['sub'];
$email    = $gUser['email'];
$fullName = $gUser['name'] ?? explode('@', $email)[0];

// ── Step 3: Find or create user ───────────────────────────────────────────────
$stmt = $conn->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.google_id = ? OR u.email = ? LIMIT 1");
$stmt->bind_param("ss", $googleId, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ── Existing user ─────────────────────────────────────────────────────────
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['status'] !== 'Active') {
        redirect('views/Auth/login.php?error=blocked');
    }

    // Bind google_id if first time logging in via Google
    if (empty($user['google_id'])) {
        $upd = $conn->prepare("UPDATE users SET google_id = ?, auth_provider = 'google' WHERE id = ?");
        $upd->bind_param("ss", $googleId, $user['id']);
        $upd->execute();
        $upd->close();
    }

} else {
    // ── New user — register via Google ────────────────────────────────────────
    $stmt->close();

    // Generate ID
    $res     = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
    $lastId  = ($res && $res->num_rows) ? $res->fetch_assoc()['id'] : 'USR0000';
    $num     = (int) substr($lastId, 3) + 1;
    $newId   = "USR" . str_pad($num, 4, "0", STR_PAD_LEFT);
    $uname   = 'g_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0])) . rand(10, 99);
    $dummyPw = hash_password(bin2hex(random_bytes(16)));

    $ins = $conn->prepare("INSERT INTO users (id, full_name, username, email, password, image, role_id, status, google_id, auth_provider) VALUES (?, ?, ?, ?, ?, 'default.png', 2, 'Active', ?, 'google')");
    $ins->bind_param("ssssss", $newId, $fullName, $uname, $email, $dummyPw, $googleId);

    if (!$ins->execute()) {
        redirect('views/Auth/login.php?error=register_failed');
    }
    $ins->close();

    // Re-fetch with role
    $get = $conn->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ? LIMIT 1");
    $get->bind_param("s", $newId);
    $get->execute();
    $user = $get->get_result()->fetch_assoc();
    $get->close();
}

// ── Step 4: Set session & redirect ───────────────────────────────────────────
$_SESSION['user_id']   = $user['id'];
$_SESSION['username']  = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['image']     = $user['image'];
$_SESSION['email']     = $user['email'];
$_SESSION['phone']     = $user['phone'] ?? '';
$_SESSION['role']      = $user['role_name'];
$_SESSION['last_login'] = time();

$conn->close();
redirect('views/dashboard/index.php');
