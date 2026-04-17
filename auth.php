<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

function isAdminAuthenticated(): bool
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function attemptAdminLogin(string $username, string $password): bool
{
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }

    return false;
}

function requireAdminAccess(): void
{
    if (!isAdminAuthenticated()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Admin access required.',
        ]);
        exit;
    }
}

function logoutAdmin(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}
