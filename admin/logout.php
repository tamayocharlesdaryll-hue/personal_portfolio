<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

redirect('login.php');
