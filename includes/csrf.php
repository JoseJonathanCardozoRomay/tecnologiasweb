<?php

require_once __DIR__ . '/sesion.php';

/**
 * Genera un token de seguridad único para la sesión.
 */
function obtenerTokenCsrf(): string
{
    if (
        !isset($_SESSION['token_csrf'])
        || !is_string($_SESSION['token_csrf'])
        || strlen($_SESSION['token_csrf']) !== 64
    ) {
        $_SESSION['token_csrf'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['token_csrf'];
}

/**
 * Genera el campo oculto utilizado en los formularios POST.
 */
function campoCsrf(): string
{
    $token = htmlspecialchars(
        obtenerTokenCsrf(),
        ENT_QUOTES,
        'UTF-8'
    );

    return '<input type="hidden" name="token_csrf" value="'
        . $token
        . '">';
}

/**
 * Comprueba que el token enviado pertenezca a la sesión actual.
 */
function validarTokenCsrf(): bool
{
    $tokenSesion = $_SESSION['token_csrf'] ?? '';
    $tokenEnviado = $_POST['token_csrf'] ?? '';

    if (
        !is_string($tokenSesion)
        || !is_string($tokenEnviado)
        || $tokenSesion === ''
        || $tokenEnviado === ''
    ) {
        return false;
    }

    return hash_equals(
        $tokenSesion,
        $tokenEnviado
    );
}

/**
 * Elimina el token actual.
 *
 * Puede utilizarse al cerrar sesión o después de regenerar
 * completamente los datos de autenticación.
 */
function eliminarTokenCsrf(): void
{
    unset($_SESSION['token_csrf']);
}