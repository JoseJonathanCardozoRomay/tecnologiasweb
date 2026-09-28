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
    'mg.reuniones.ver',
    '../index.php'
);

$modeloRevision = new RevisionSeguimientoModel($pdo);

$reuniones = $modeloRevision
    ->listarReunionesPendientes();

$mensajes = [
    'validada' => 'La reunión fue validada correctamente.',
    'observada' => 'La reunión fue devuelta al tutor con observaciones.',
    'no_encontrada' => 'No se encontró la reunión solicitada.',
    'error' => 'No fue posible completar la validación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrada', 'error'],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Validación de reuniones';
$rutaBase = '../';

require_once __DIR__
    . '/../views/revision/reuniones_listar.php';