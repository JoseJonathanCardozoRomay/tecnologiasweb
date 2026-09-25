<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../models/ExpedienteEtapaModel.php';
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
    'mg.expedientes.ver',
    '../index.php'
);

$modeloExpediente = new ExpedienteMgModel($pdo);
$modeloEtapa = new ExpedienteEtapaModel($pdo);

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idExpediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

$expediente = $modeloExpediente->buscarPorId(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

// Recuperamos el historial sin modificarlo
$historialEtapas = $modeloEtapa->listarPorExpediente(
    $idExpediente
);

$etapaAbierta = $modeloEtapa->buscarEtapaAbierta(
    $idExpediente
);

// Permisos utilizados por las acciones de la ficha
$puedeEditar = usuarioTienePermiso(
    'mg.expedientes.editar'
);

$puedeCambiarEtapa = usuarioTienePermiso(
    'mg.expedientes.cambiar_etapa'
);

$puedeAsignarTutor = usuarioTienePermiso(
    'mg.tutores.asignar'
);

// Mensajes de acciones futuras de la ficha
$mensajes = [
    'actualizado' => 'El expediente fue actualizado correctamente.',
    'etapa_actualizada' => 'La etapa fue actualizada correctamente.',
    'estado_actualizado' => 'El estado fue actualizado correctamente.',
    'error' => 'No fue posible completar la operación.'
];

$estadoRecibido = $_GET['estado'] ?? '';

$estado = is_string($estadoRecibido)
    ? $estadoRecibido
    : '';

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = $estado === 'error'
    ? 'danger'
    : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Detalle del expediente';
$rutaBase = '../';

require_once __DIR__
    . '/../views/expedientes/ver.php';