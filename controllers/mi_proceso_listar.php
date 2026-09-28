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

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloSeguimiento = new SeguimientoEstudianteModel(
    $pdo
);

$expedientes = $modeloSeguimiento->listarExpedientes(
    $idUsuario
);

$tituloPagina = 'Mi proceso de grado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/mi_proceso/procesos.php';