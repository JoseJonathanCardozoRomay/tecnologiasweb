<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__
    . '/../models/DefensaOperacionModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.defensas.reprogramar',
    '../index.php'
);

$modeloDefensa = new DefensaModel($pdo);
$modeloOperacion = new DefensaOperacionModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDefensa = filter_input(
        INPUT_POST,
        'id_defensa',
        FILTER_VALIDATE_INT
    );
} else {
    $idDefensa = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idDefensa) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$defensa = $modeloDefensa->buscarPorId(
    $idDefensa
);

if (
    !$defensa
    || $defensa['estado'] !== 'programada'
    || (int) $defensa['defensa_vigente'] !== 1
) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$fecha = $defensa['fecha'];
$horaInicio = substr(
    $defensa['hora_inicio'],
    0,
    5
);
$horaFin = substr(
    $defensa['hora_fin'],
    0,
    5
);
$ambiente = $defensa['ambiente'];
$motivoCambio = '';
$autorizadoPor = $defensa['autorizado_por'] ?? '';
$referenciaAutorizacion = $defensa[
    'referencia_autorizacion'
] ?? '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = trim(
        $_POST['fecha'] ?? ''
    );

    $horaInicio = trim(
        $_POST['hora_inicio'] ?? ''
    );

    $horaFin = trim(
        $_POST['hora_fin'] ?? ''
    );

    $ambiente = trim(
        $_POST['ambiente'] ?? ''
    );

    $motivoCambio = trim(
        $_POST['motivo_cambio'] ?? ''
    );

    $autorizadoPor = trim(
        $_POST['autorizado_por'] ?? ''
    );

    $referenciaAutorizacion = trim(
        $_POST['referencia_autorizacion'] ?? ''
    );

    $fechaValida = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    $fechaCorrecta = $fechaValida
        && $fechaValida->format('Y-m-d') === $fecha;

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página.';
    } elseif (!$fechaCorrecta) {
        $error = 'Selecciona una fecha válida.';
    } elseif ($fecha < date('Y-m-d')) {
        $error = 'La nueva fecha no puede encontrarse en el pasado.';
    } elseif (
        !preg_match('/^\d{2}:\d{2}$/', $horaInicio)
        || !preg_match('/^\d{2}:\d{2}$/', $horaFin)
    ) {
        $error = 'Ingresa un horario válido.';
    } elseif ($horaFin <= $horaInicio) {
        $error = 'La hora de finalización debe ser posterior a la hora de inicio.';
    } elseif (
        mb_strlen($ambiente) < 2
        || mb_strlen($ambiente) > 100
    ) {
        $error = 'El ambiente debe contener entre 2 y 100 caracteres.';
    } elseif (
        mb_strlen($motivoCambio) < 5
        || mb_strlen($motivoCambio) > 255
    ) {
        $error = 'El motivo debe contener entre 5 y 255 caracteres.';
    } elseif (mb_strlen($autorizadoPor) > 150) {
        $error = 'El nombre de la autoridad no puede superar los 150 caracteres.';
    } elseif (
        mb_strlen($referenciaAutorizacion) > 100
    ) {
        $error = 'La referencia no puede superar los 100 caracteres.';
    } elseif (
        ($autorizadoPor === '')
        !== ($referenciaAutorizacion === '')
    ) {
        $error = 'La autoridad y la referencia deben completarse juntas.';
    } else {
        $conflicto = $modeloDefensa->buscarConflicto(
            (int) $defensa['id_expediente'],
            $defensa['etapa'],
            $fecha,
            $horaInicio,
            $horaFin,
            $ambiente,
            $idDefensa
        );

        $mensajesConflicto = [
            'ambiente' => 'El ambiente ya está ocupado en ese horario.',
            'estudiante' => 'El estudiante ya tiene otra defensa en ese horario.',
            'tribunal' => 'Uno de los tribunales ya participa en otra defensa en ese horario.'
        ];

        if ($conflicto !== null) {
            $error = $mensajesConflicto[$conflicto]
                ?? 'Existe un cruce de horario.';
        } else {
            try {
                $idNuevaDefensa = $modeloOperacion
                    ->reprogramar(
                        $idDefensa,
                        $fecha,
                        $horaInicio,
                        $horaFin,
                        $ambiente,
                        $motivoCambio,
                        $autorizadoPor !== ''
                            ? $autorizadoPor
                            : null,
                        $referenciaAutorizacion !== ''
                            ? $referenciaAutorizacion
                            : null,
                        (int) $usuarioSesion['id_usuario']
                    );

                if ($idNuevaDefensa === 0) {
                    $error = 'La defensa ya no se encuentra disponible para reprogramar.';
                } else {
                    header(
                        'Location: defensa_ver.php?id='
                        . $idNuevaDefensa
                        . '&estado=reprogramada'
                    );
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'No fue posible reprogramar la defensa.';
            }
        }
    }
}

$tituloPagina = 'Reprogramar defensa';
$rutaBase = '../';

require_once __DIR__
    . '/../views/defensas/reprogramar.php';