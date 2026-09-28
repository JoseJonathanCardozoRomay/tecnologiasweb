<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudTutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(['tutor'], '../index.php');

$usuarioSesion = obtenerUsuarioSesion();
$modeloSolicitud = new SolicitudTutoriaModel($pdo);

$tutor = $modeloSolicitud->buscarTutorPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header('Location: ../index.php?estado=sin_perfil_tutor');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estadoResultado = 'datos_invalidos';

    if (!validarTokenCsrf()) {
        $estadoResultado = 'token_invalido';
    } else {
        $idTutoria = filter_input(
            INPUT_POST,
            'id_tutoria',
            FILTER_VALIDATE_INT
        );

        $accion = is_string($_POST['accion'] ?? null)
            ? $_POST['accion']
            : '';

        if ($idTutoria) {
            $fecha = is_string($_POST['fecha'] ?? null)
                ? trim($_POST['fecha'])
                : '';

            $horaInicio = is_string($_POST['hora_inicio'] ?? null)
                ? trim($_POST['hora_inicio'])
                : '';

            $horaFin = is_string($_POST['hora_fin'] ?? null)
                ? trim($_POST['hora_fin'])
                : '';

            $modalidad = is_string($_POST['modalidad'] ?? null)
                ? trim($_POST['modalidad'])
                : '';

            $lugarOEnlace = is_string(
                $_POST['lugar_o_enlace'] ?? null
            )
                ? trim($_POST['lugar_o_enlace'])
                : '';

            $estadoResultado = $modeloSolicitud->responderTutor(
                (int) $idTutoria,
                (int) $usuarioSesion['id_usuario'],
                $accion,
                $fecha,
                $horaInicio,
                $horaFin,
                $modalidad,
                $lugarOEnlace !== '' ? $lugarOEnlace : null
            );
        }
    }

    header(
        'Location: solicitudes_tutor.php?estado='
        . rawurlencode($estadoResultado)
    );
    exit;
}

$solicitudes = $modeloSolicitud->listarPendientesTutor(
    (int) $usuarioSesion['id_usuario']
);

$mensajes = [
    'pendiente_aprobacion' => 'La propuesta fue enviada a administración o coordinación para su aprobación.',
    'rechazada' => 'Rechazaste la solicitud.',
    'datos_invalidos' => 'Revisa fecha, horario, modalidad y aula. Para una tutoría presencial, indica el aula.',
    'token_invalido' => 'El formulario venció. Recarga la página e inténtalo otra vez.',
    'ya_procesada' => 'La solicitud ya fue atendida o no te corresponde.',
    'tutor_no_disponible' => 'No estás habilitado para esa materia o periodo.',
    'sin_cupo' => 'Ya alcanzaste el máximo de estudiantes para este periodo.',
    'horario_ocupado' => 'Ese horario se cruza con otra tutoría tuya o del estudiante.',
    'error' => 'No fue posible procesar la solicitud.'
];

$estado = is_string($_GET['estado'] ?? null)
    ? $_GET['estado']
    : '';

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'datos_invalidos',
        'token_invalido',
        'ya_procesada',
        'tutor_no_disponible',
        'sin_cupo',
        'horario_ocupado',
        'error'
    ],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Solicitudes recibidas';
$rutaBase = '../';

require_once __DIR__ . '/../views/solicitudes/tutor.php';