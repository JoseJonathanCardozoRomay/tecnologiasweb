<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CalendarioTutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.calendario.ver_propios',
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloCalendario = new CalendarioTutorModel($pdo);

$hitos = $modeloCalendario->listarPorTutor(
    $idUsuario
);

$fechaActual = new DateTimeImmutable('today');

$tituloPagina = 'Calendario de tutorados';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tutorados/calendario.php';