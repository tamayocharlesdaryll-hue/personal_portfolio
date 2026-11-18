<?php

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function login_attempt_allowed() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
        return false;
    }

    if (isset($_SESSION['login_locked_until']) && time() >= $_SESSION['login_locked_until']) {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['login_locked_until']);
    }

    return true;
}

function add_login_attempt() {
    $_SESSION['login_attempts']++;

    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_locked_until'] = time() + 300;
    }
}
