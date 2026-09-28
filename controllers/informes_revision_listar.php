<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RevisionSeguimientoModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.informes.ver',
    '../index.php'
);

$modeloRevision = new RevisionSeguimientoModel($pdo);

$informes = $modeloRevision
    ->listarInformesPendientes();

$mensajes = [
    'aprobado' => 'El informe fue aprobado correctamente.',
    'observado' => 'El informe fue devuelto al tutor con observaciones.',
    'no_encontrado' => 'No se encontró el informe solicitado.',
    'error' => 'No fue posible completar la revisión.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'error'],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Revisión de informes';
$rutaBase = '../';

require_once __DIR__
    . '/../views/revision/informes_listar.php';