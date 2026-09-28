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
    'mg.calendario.ver_propio',
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloSeguimiento = new SeguimientoEstudianteModel(
    $pdo
);

$hitos = $modeloSeguimiento->listarCalendario(
    $idUsuario
);

$fechaActual = new DateTimeImmutable('today');

$tituloPagina = 'Mi calendario';
$rutaBase = '../';

require_once __DIR__
    . '/../views/mi_proceso/calendario.php';