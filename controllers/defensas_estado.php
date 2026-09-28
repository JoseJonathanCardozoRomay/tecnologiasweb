<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/DefensaOperacionModel.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.defensas.reprogramar',
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

$idDefensa = filter_input(
    INPUT_POST,
    'id_defensa',
    FILTER_VALIDATE_INT
);

$nuevoEstado = $_POST['estado'] ?? '';

if (
    !$idDefensa
    || !in_array(
        $nuevoEstado,
        ['realizada', 'cancelada'],
        true
    )
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

$modeloOperacion = new DefensaOperacionModel($pdo);
$modeloDefensa = new DefensaModel($pdo);
$modeloBitacora = new BitacoraModel($pdo);

$defensaAnterior = $modeloDefensa->buscarPorId(
    $idDefensa
);

if (!$defensaAnterior) {
    header(
        'Location: defensas_listar.php?estado=no_encontrada'
    );
    exit;
}

try {
    $resultado = $modeloOperacion->cambiarEstado(
        $idDefensa,
        $nuevoEstado
    );

    if (!$resultado) {
        header(
            'Location: defensa_ver.php?id='
            . $idDefensa
            . '&estado=error'
        );
        exit;
    }

    $defensaActualizada = $modeloDefensa->buscarPorId(
        $idDefensa
    );

    $usuarioSesion = obtenerUsuarioSesion();

    // Guardamos únicamente los datos importantes del cambio
    $modeloBitacora->registrar(
        isset($usuarioSesion['id_usuario'])
            ? (int) $usuarioSesion['id_usuario']
            : null,
        'cambiar_estado_defensa',
        'defensas_mg',
        $idDefensa,
        [
            'estado' => $defensaAnterior['estado'],
            'defensa_vigente' => (int)
                $defensaAnterior['defensa_vigente']
        ],
        [
            'estado' => $defensaActualizada['estado'],
            'defensa_vigente' => (int)
                $defensaActualizada['defensa_vigente']
        ]
    );

    header(
        'Location: defensa_ver.php?id='
        . $idDefensa
        . '&estado=actualizada'
    );
    exit;
} catch (Throwable $e) {
    error_log(
        'Error al actualizar la defensa: '
        . $e->getMessage()
    );

    header(
        'Location: defensa_ver.php?id='
        . $idDefensa
        . '&estado=error'
    );
    exit;
}