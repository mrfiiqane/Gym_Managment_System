<?php
// Security Helpers

/**
 * Clean user input to prevent XSS
 */
function s($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Password Hashing
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Password Verification
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check user role
 * Supports single role (string) or multiple roles (array)
 */
function has_role($role) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    
    if (is_array($role)) {
        return in_array($_SESSION['role'], $role);
    }
    
    return $_SESSION['role'] === $role;
}



