<?php
require_once __DIR__ . '/sesion.php';

function csrf_token()
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_campo()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_validar()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token)) {
        http_response_code(403);
        exit('Solicitud expirada o inválida. Regresa al formulario e inténtalo nuevamente.');
    }
}
