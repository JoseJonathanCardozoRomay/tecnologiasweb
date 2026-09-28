<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.defensas.ver',
    '../index.php'
);

$modeloDefensa = new DefensaModel($pdo);
$modeloTribunal = new TribunalModel($pdo);

$idDefensa = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idDefensa) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$defensa = $modeloDefensa->buscarPorId(
    $idDefensa
);

if (!$defensa) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$tribunalesExpediente = $modeloTribunal
    ->listarPorExpediente(
        (int) $defensa['id_expediente']
    );

$tribunalesDefensa = array_filter(
    $tribunalesExpediente,
    fn(array $tribunal): bool =>
        $tribunal['etapa'] === $defensa['etapa']
        && $tribunal['estado'] === 'vigente'
);

$puedeReprogramar = usuarioTienePermiso(
    'mg.defensas.reprogramar'
);

$puedeRegistrarCalificacion = usuarioTienePermiso(
    'mg.calificaciones.editar'
);

$mensajes = [
    'actualizada' => 'El estado de la defensa fue actualizado.',
    'reprogramada' => 'La defensa fue reprogramada correctamente.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = $estado === 'error'
    ? 'danger'
    : 'success';

$tituloPagina = 'Detalle de defensa';
$rutaBase = '../';

require_once __DIR__
    . '/../views/defensas/ver.php';