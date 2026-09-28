<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__ . '/../includes/permisos.php';

// Esta pantalla corresponde a Coordinación y Administración
requerirPermiso(
    'mg.defensas.ver',
    '../index.php'
);

$modeloDefensa = new DefensaModel($pdo);

$busqueda = trim(
    $_GET['buscar'] ?? ''
);

$defensas = $modeloDefensa->listar(
    $busqueda
);

$expedientesDisponibles = $modeloDefensa
    ->listarExpedientesDisponibles();

$puedeProgramar = usuarioTienePermiso(
    'mg.defensas.programar'
);

$puedeReprogramar = usuarioTienePermiso(
    'mg.defensas.reprogramar'
);

$mensajes = [
    'programada' => 'La defensa fue programada correctamente.',
    'reprogramada' => 'La defensa fue reprogramada y el historial fue conservado.',
    'realizada' => 'La defensa fue marcada como realizada.',
    'cancelada' => 'La defensa fue cancelada correctamente.',
    'no_encontrado' => 'No se encontró la defensa solicitada.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'no_encontrado',
        'error'
    ],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Defensas de Modalidades de Grado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/defensas/listar.php';