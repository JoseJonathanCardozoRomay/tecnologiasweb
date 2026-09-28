<?php

// La conexión permite comprobar si la cuenta continúa activa
require_once __DIR__ . '/../config/conexion.php';

/*
 * Configuración segura de la sesión.
 * La cookie Secure se activa automáticamente cuando se utiliza HTTPS.
 */
if (session_status() === PHP_SESSION_NONE) {
    $conexionSegura = isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== ''
        && $_SERVER['HTTPS'] !== 'off';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');

    session_name('tutorias_sesion');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $conexionSegura,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

/**
 * Elimina los datos de una sesión que dejó de ser válida.
 */
function limpiarSesionActual(): void
{
    $_SESSION = [];

    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    // Eliminamos también la cookie almacenada en el navegador
    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $parametros['path'],
                'domain' => $parametros['domain'],
                'secure' => $parametros['secure'],
                'httponly' => $parametros['httponly'],
                'samesite' => $parametros['samesite']
                    ?? 'Lax'
            ]
        );
    }

    // Creamos un identificador vacío nuevo para invalidar el anterior
    session_regenerate_id(true);
}

/**
 * Controla el tiempo máximo y la inactividad de la sesión.
 */
function validarVigenciaSesion(): void
{
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        return;
    }

    $ahora = time();

    // La sesión se cierra después de 30 minutos sin actividad
    $tiempoInactividad = 30 * 60;

    // Una sesión autenticada puede durar como máximo ocho horas
    $duracionMaxima = 8 * 60 * 60;

    $ultimaActividad = (int) (
        $_SESSION['ultima_actividad'] ?? $ahora
    );

    $inicioSesion = (int) (
        $_SESSION['inicio_sesion'] ?? $ahora
    );

    if (
        ($ahora - $ultimaActividad) > $tiempoInactividad
        || ($ahora - $inicioSesion) > $duracionMaxima
    ) {
        limpiarSesionActual();
        return;
    }

    $_SESSION['inicio_sesion'] = $inicioSesion;
    $_SESSION['ultima_actividad'] = $ahora;
}

/**
 * Regenera periódicamente el identificador para reducir el riesgo
 * de fijación o reutilización de una sesión.
 */
function renovarIdentificadorSesion(): void
{
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        return;
    }

    $ahora = time();
    $ultimaRenovacion = (int) (
        $_SESSION['ultima_renovacion'] ?? 0
    );

    // Renovamos el identificador cada quince minutos
    if (
        $ultimaRenovacion === 0
        || ($ahora - $ultimaRenovacion) >= 15 * 60
    ) {
        session_regenerate_id(true);
        $_SESSION['ultima_renovacion'] = $ahora;
    }
}

/**
 * Comprueba que el usuario continúe activo y conserva actualizado
 * el rol almacenado en la sesión.
 */
function validarEstadoSesion(PDO $conexion): void
{
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        return;
    }

    $sql = "
        SELECT
            u.estado,
            r.nombre_rol
        FROM usuarios AS u
        INNER JOIN roles AS r
            ON r.id_rol = u.id_rol
        WHERE u.id_usuario = :id_usuario
        LIMIT 1
    ";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        'id_usuario' => (int)
            $_SESSION['usuario']['id_usuario']
    ]);

    $usuarioActual = $consulta->fetch(
        PDO::FETCH_ASSOC
    );

    if (
        !$usuarioActual
        || $usuarioActual['estado'] !== 'activo'
    ) {
        limpiarSesionActual();
        return;
    }

    // Si un administrador cambia el rol, la sesión se actualiza
    $_SESSION['usuario']['rol'] =
        $usuarioActual['nombre_rol'];
}

// Ejecutamos las comprobaciones en cada página privada o pública
validarVigenciaSesion();
renovarIdentificadorSesion();
validarEstadoSesion($pdo);

/**
 * Verifica si existe un usuario autenticado.
 */
function usuarioAutenticado(): bool
{
    return isset(
        $_SESSION['usuario']['id_usuario']
    );
}

/**
 * Devuelve los datos del usuario autenticado.
 */
function obtenerUsuarioSesion(): ?array
{
    return usuarioAutenticado()
        ? $_SESSION['usuario']
        : null;
}

/**
 * Evita el acceso a páginas privadas sin una sesión válida.
 */
function requerirSesion(
    string $rutaLogin = '../controllers/login.php'
): void {
    if (!usuarioAutenticado()) {
        header('Location: ' . $rutaLogin);
        exit;
    }
}

/**
 * Permite el acceso solamente a los roles indicados.
 */
function requerirRol(
    array $rolesPermitidos,
    string $rutaInicio = '../index.php'
): void {
    requerirSesion();

    $usuario = obtenerUsuarioSesion();
    $rolActual = $usuario['rol'] ?? '';

    if (!in_array($rolActual, $rolesPermitidos, true)) {
        header(
            'Location: '
            . $rutaInicio
            . '?estado=acceso_denegado'
        );
        exit;
    }
}