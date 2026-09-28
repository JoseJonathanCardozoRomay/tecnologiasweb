<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/CalificacionEstudianteModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.calificaciones.ver_publicadas',
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();

$modeloCalificacion = new CalificacionEstudianteModel(
    $pdo
);

$calificaciones = $modeloCalificacion
    ->listarPorUsuario(
        (int) $usuarioSesion['id_usuario']
    );

$tituloPagina = 'Mis calificaciones';
$rutaBase = '../';

require_once __DIR__
    . '/../views/mi_proceso/calificaciones.php';