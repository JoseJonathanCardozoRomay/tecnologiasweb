<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudTutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

$modeloSolicitud = new SolicitudTutoriaModel($pdo);

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

        $observacion = is_string($_POST['observacion'] ?? null)
            ? trim($_POST['observacion'])
            : '';

        if ($idTutoria) {
            $usuarioSesion = obtenerUsuarioSesion();
            $estadoResultado = $modeloSolicitud->resolverRevision(
                (int) $idTutoria,
                $accion,
                $observacion,
                (int) $usuarioSesion['id_usuario']
            );
        }
    }

    header(
        'Location: solicitudes_listar.php?estado='
        . rawurlencode($estadoResultado)
    );
    exit;
}

$solicitudes = $modeloSolicitud->listarPendientesAprobacion();

$mensajes = [
    'confirmada' => 'La tutoría fue aprobada. Ya puede ser consultada por el estudiante y el tutor.',
    'observada' => 'La tutoría se devolvió al tutor con las observaciones.',
    'observacion_invalida' => 'Escribe una observación de hasta 500 caracteres.',
    'ya_procesada' => 'La solicitud ya fue procesada o no existe.',
    'datos_invalidos' => 'Los datos enviados no son válidos.',
    'token_invalido' => 'El formulario venció. Recarga la página e inténtalo otra vez.',
    'error' => 'No fue posible revisar la solicitud.'
];

$estado = is_string($_GET['estado'] ?? null)
    ? $_GET['estado']
    : '';

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'observacion_invalida',
        'ya_procesada',
        'datos_invalidos',
        'token_invalido',
        'error'
    ],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Aprobación de tutorías';
$rutaBase = '../';

require_once __DIR__ . '/../views/solicitudes/listar.php';
