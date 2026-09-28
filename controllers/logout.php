<?php

require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarTokenCsrf()) {
    header('Location: ../index.php');
    exit;
}

// Eliminamos todos los datos guardados en la sesión
$_SESSION = [];

// Eliminamos también la cookie utilizada por PHP
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $parametros['path'],
        'domain' => $parametros['domain'],
        'secure' => $parametros['secure'],
        'httponly' => $parametros['httponly'],
        'samesite' => $parametros['samesite'] ?? 'Lax'
    ]);
}

// Cerramos completamente la sesión actual
session_destroy();

header('Location: ../index.php');
exit;
