<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

if (!function_exists('csrf_generar')) {
    function csrf_generar() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_validar')) {
    function csrf_validar($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>