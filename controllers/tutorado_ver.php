<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.expedientes.ver_propios',
    '../index.php'
);

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idExpediente) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloAsignacion = new AsignacionTutorModel($pdo);

// También comprobamos que el expediente pertenezca al tutor.
$tutorado = $modeloAsignacion->buscarTutorado(
    $idExpediente,
    $idUsuario
);

if (!$tutorado) {
    header(
        'Location: tutorados_listar.php?estado=no_encontrado'
    );
    exit;
}

$tituloPagina = 'Seguimiento del tutorado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tutorados/ver.php';