<?php
declare(strict_types=1);

require_once __DIR__ . '/../session_bootstrap.php';
startAppSession();

// clear all session variables
$_SESSION = [];

// destroy the session
session_destroy();

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly'],
    );
}

echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logging out...</title>
    <script src="../assets/js/main.js" defer></script>
</head>
<body data-admin-auth-action="clear" data-admin-auth-redirect="login.php">
</body>
</html>
HTML;
exit;