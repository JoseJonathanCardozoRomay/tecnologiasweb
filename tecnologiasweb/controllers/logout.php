<?php

require_once __DIR__ . '/../includes/sesion.php';

// Eliminamos todos los datos guardados en la sesión
$_SESSION = [];

// Eliminamos también la cookie utilizada por PHP
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// Cerramos completamente la sesión actual
session_destroy();

header('Location: ../index.php');
exit;