<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.defensas.programar',
    '../index.php'
);

$modeloDefensa = new DefensaModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

$expedientesDisponibles = $modeloDefensa
    ->listarExpedientesDisponibles();

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
) ?: null;

$fecha = '';
$horaInicio = '';
$horaFin = '';
$ambiente = '';
$autorizadoPor = '';
$referenciaAutorizacion = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idExpediente = filter_input(
        INPUT_POST,
        'id_expediente',
        FILTER_VALIDATE_INT
    );

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

    $autorizadoPor = trim(
        $_POST['autorizado_por'] ?? ''
    );

    $referenciaAutorizacion = trim(
        $_POST['referencia_autorizacion'] ?? ''
    );

    $expediente = $idExpediente
        ? $modeloDefensa->buscarExpediente($idExpediente)
        : null;

    $fechaValida = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    $fechaCorrecta = $fechaValida
        && $fechaValida->format('Y-m-d') === $fecha;

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página.';
    } elseif (
        !$expediente
        || $expediente['estado'] !== 'activo'
        || !in_array(
            $expediente['etapa_actual'],
            ['mg1', 'mg2'],
            true
        )
    ) {
        $error = 'Selecciona un expediente activo en MG1 o MG2.';
    } elseif (!$fechaCorrecta) {
        $error = 'Selecciona una fecha válida.';
    } elseif ($fecha < date('Y-m-d')) {
        $error = 'La defensa no puede programarse en una fecha pasada.';
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
        $etapa = $expediente['etapa_actual'];

        $cantidadTribunales = $modeloDefensa
            ->contarTribunales(
                $idExpediente,
                $etapa
            );

        if ($cantidadTribunales === 0) {
            $error = 'Primero debes asignar tribunales para esta etapa.';
        } else {
            $conflicto = $modeloDefensa->buscarConflicto(
                $idExpediente,
                $etapa,
                $fecha,
                $horaInicio,
                $horaFin,
                $ambiente
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
                    $modeloDefensa->crear(
                        $idExpediente,
                        $etapa,
                        $fecha,
                        $horaInicio,
                        $horaFin,
                        $ambiente,
                        $autorizadoPor !== ''
                            ? $autorizadoPor
                            : null,
                        $referenciaAutorizacion !== ''
                            ? $referenciaAutorizacion
                            : null,
                        (int) $usuarioSesion['id_usuario']
                    );

                    header(
                        'Location: defensas_listar.php?estado=programada'
                    );
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $error = 'El expediente ya posee una defensa vigente en esta etapa.';
                    } else {
                        $error = 'No fue posible programar la defensa.';
                    }
                }
            }
        }
    }
}

$tituloPagina = 'Programar defensa';
$rutaBase = '../';

require_once __DIR__
    . '/../views/defensas/programar.php';