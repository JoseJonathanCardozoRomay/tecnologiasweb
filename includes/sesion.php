<?php

const SESION_TIEMPO_INACTIVIDAD = 1800;

function iniciarSesionSegura()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function controlarInactividad()
{
    if (empty($_SESSION['id_usuario'])) {
        return;
    }

    $ahora = time();
    if (!empty($_SESSION['ultima_actividad']) && ($ahora - (int) $_SESSION['ultima_actividad']) > SESION_TIEMPO_INACTIVIDAD) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'] ?? '', (bool) $parametros['secure'], (bool) $parametros['httponly']);
        }
        session_destroy();
        session_start();
        $_SESSION['login_error'] = 'Tu sesión expiró por inactividad. Inicia sesión nuevamente.';
        header('Location: /views/login/login.php');
        exit;
    }
    $_SESSION['ultima_actividad'] = $ahora;
}

iniciarSesionSegura();
controlarInactividad();
