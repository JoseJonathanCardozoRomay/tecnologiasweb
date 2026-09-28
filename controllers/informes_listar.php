<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../models/InformeAvanceModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.informes.ver_propios',
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
$modeloInforme = new InformeAvanceModel($pdo);

$tutorado = $modeloAsignacion->buscarTutorado(
    $idExpediente,
    $idUsuario
);

if (!$tutorado) {
    header('Location: tutorados_listar.php');
    exit;
}

$informes = $modeloInforme->listarPorExpediente(
    $idExpediente,
    $idUsuario
);

$mensajes = [
    'creado' => 'El informe fue guardado como borrador.',
    'presentado' => 'El informe fue presentado correctamente.',
    'no_encontrado' => 'No se encontró el informe solicitado.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'error'],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Informes de avance';
$rutaBase = '../';

require_once __DIR__
    . '/../views/informes/listar.php';