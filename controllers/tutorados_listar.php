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

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloAsignacion = new AsignacionTutorModel($pdo);

// El modelo devuelve únicamente asignaciones aceptadas y vigentes.
$tutorados = $modeloAsignacion->listarTutorados(
    $idUsuario
);

$tituloPagina = 'Mis tutorados';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tutorados/listar.php';