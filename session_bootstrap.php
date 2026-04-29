<?php
declare(strict_types=1);

if (!function_exists('startAppSession')) {
    function startAppSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionLifetime = 60 * 60 * 24 * 30; 

        ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
        session_set_cookie_params([
            'lifetime' => $sessionLifetime,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}

