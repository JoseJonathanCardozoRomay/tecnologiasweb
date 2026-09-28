<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../models/ReunionMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.reuniones.ver_propias',
    '../index.php'
);

$idExpediente = filter_input(
    INPUT_GET,
    'expediente',
    FILTER_VALIDATE_INT
);

if (!$idExpediente) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloAsignacion = new AsignacionTutorModel($pdo);
$modeloReunion = new ReunionMgModel($pdo);

// Impedimos consultar las reuniones de otro tutor.
$tutorado = $modeloAsignacion->buscarTutorado(
    $idExpediente,
    $idUsuario
);

if (!$tutorado) {
    header('Location: tutorados_listar.php');
    exit;
}

$reuniones = $modeloReunion->listarPorExpediente(
    $idExpediente,
    $idUsuario
);

$mensajes = [
    'creada' => 'La reunión fue programada correctamente.',
    'realizada' => 'La asistencia y el resultado fueron registrados correctamente.',
    'cancelada' => 'La reunión fue cancelada correctamente.',
    'no_encontrada' => 'No se encontró la reunión solicitada.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrada', 'error'],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Reuniones del tutorado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reuniones/listar.php';