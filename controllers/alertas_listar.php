<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AlertaMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.alertas.ver',
    '../index.php'
);

$modeloAlerta = new AlertaMgModel($pdo);

$alertas = $modeloAlerta->calcular();

$puedeAtender = usuarioTienePermiso(
    'mg.alertas.atender'
);

$resumen = [
    'alta' => 0,
    'media' => 0,
    'baja' => 0
];

foreach ($alertas as $alerta) {
    if (isset($resumen[$alerta['severidad']])) {
        $resumen[$alerta['severidad']]++;
    }
}

$mensajes = [
    'atendida' => 'La alerta fue marcada como atendida.',
    'no_encontrada' => 'La alerta ya no se encuentra activa.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = $estado === 'atendida'
    ? 'success'
    : 'danger';

$tituloPagina = 'Alertas académicas';
$rutaBase = '../';

require_once __DIR__
    . '/../views/alertas/listar.php';