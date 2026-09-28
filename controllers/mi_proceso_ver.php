<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SeguimientoEstudianteModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    ['estudiante'],
    '../index.php'
);

requerirPermiso(
    'mg.expedientes.ver_propio',
    '../index.php'
);

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idExpediente) {
    header('Location: mi_proceso_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloSeguimiento = new SeguimientoEstudianteModel(
    $pdo
);

// Todas las consultas vuelven a comprobar la propiedad.
$expediente = $modeloSeguimiento->buscarExpediente(
    $idExpediente,
    $idUsuario
);

if (!$expediente) {
    header('Location: mi_proceso_listar.php');
    exit;
}

$reuniones = $modeloSeguimiento->listarReuniones(
    $idExpediente,
    $idUsuario
);

$informes = $modeloSeguimiento
    ->listarInformesAprobados(
        $idExpediente,
        $idUsuario
    );

$tituloPagina = 'Seguimiento de mi proceso';
$rutaBase = '../';

require_once __DIR__
    . '/../views/mi_proceso/ver.php';