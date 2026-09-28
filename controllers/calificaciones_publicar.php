<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/CalificacionModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.calificaciones.publicar',
    '../index.php'
);

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !validarTokenCsrf()
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

$idCalificacion = filter_input(
    INPUT_POST,
    'id_calificacion',
    FILTER_VALIDATE_INT
);

$idDefensa = filter_input(
    INPUT_POST,
    'id_defensa',
    FILTER_VALIDATE_INT
);

$estadoRecibido = $_POST['publicada'] ?? '';

if (
    !$idCalificacion
    || !$idDefensa
    || !in_array(
        $estadoRecibido,
        ['0', '1'],
        true
    )
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

$modeloCalificacion = new CalificacionModel($pdo);
$modeloBitacora = new BitacoraModel($pdo);

$calificacionAnterior = $modeloCalificacion->buscarPorDefensa(
    $idDefensa
);

if (
    !$calificacionAnterior
    || empty($calificacionAnterior['id_calificacion'])
    || (int) $calificacionAnterior['id_calificacion']
        !== $idCalificacion
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

try {
    $publicar = $estadoRecibido === '1';

    $modeloCalificacion->cambiarPublicacion(
        $idCalificacion,
        $publicar
    );

    $calificacionActualizada =
        $modeloCalificacion->buscarPorDefensa(
            $idDefensa
        );

    if (!$calificacionActualizada) {
        throw new RuntimeException(
            'No se pudo recuperar la calificación actualizada.'
        );
    }

    $usuarioSesion = obtenerUsuarioSesion();

    $modeloBitacora->registrar(
        isset($usuarioSesion['id_usuario'])
            ? (int) $usuarioSesion['id_usuario']
            : null,
        $publicar
            ? 'publicar_calificacion'
            : 'ocultar_calificacion',
        'calificaciones_mg',
        $idCalificacion,
        [
            'id_defensa' => $idDefensa,
            'publicada' => (int)
                $calificacionAnterior['publicada']
        ],
        [
            'id_defensa' => $idDefensa,
            'publicada' => (int)
                $calificacionActualizada['publicada']
        ]
    );

    $resultado = $publicar
        ? 'publicada'
        : 'ocultada';

    header(
        'Location: calificaciones_registrar.php?id_defensa='
        . $idDefensa
        . '&estado='
        . $resultado
    );
    exit;
} catch (Throwable $e) {
    error_log(
        'Error al cambiar la publicación de la calificación: '
        . $e->getMessage()
    );

    header(
        'Location: calificaciones_registrar.php?id_defensa='
        . $idDefensa
        . '&estado=error'
    );
    exit;
}