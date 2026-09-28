<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirSesion();

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !validarTokenCsrf()
) {
    header(
        'Location: notificaciones_listar.php'
    );
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloNotificacion = new NotificacionModel($pdo);

if (
    isset($_POST['marcar_todas'])
    && $_POST['marcar_todas'] === '1'
) {
    $modeloNotificacion->marcarTodasLeidas(
        $idUsuario
    );

    header(
        'Location: notificaciones_listar.php'
    );
    exit;
}

$idNotificacion = filter_input(
    INPUT_POST,
    'id_notificacion',
    FILTER_VALIDATE_INT
);

if (!$idNotificacion) {
    header(
        'Location: notificaciones_listar.php'
    );
    exit;
}

$urlDestino = $modeloNotificacion->marcarLeida(
    $idNotificacion,
    $idUsuario
);

/*
 * Solo permitimos rutas internas generadas por el sistema.
 * Esto evita redirecciones hacia sitios externos.
 */
$urlValida = $urlDestino !== null
    && str_starts_with(
        $urlDestino,
        'controllers/'
    )
    && !str_contains($urlDestino, '..')
    && !str_contains($urlDestino, '://')
    && !str_ends_with($urlDestino, 'id=0');

if (!$urlValida) {
    header(
        'Location: notificaciones_listar.php'
    );
    exit;
}

header(
    'Location: ../' . $urlDestino
);
exit;