<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReporteMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.reportes.ver',
    '../index.php'
);

$modeloReporte = new ReporteMgModel($pdo);

$idCohorte = filter_input(
    INPUT_GET,
    'cohorte',
    FILTER_VALIDATE_INT
) ?: null;

$idModalidad = filter_input(
    INPUT_GET,
    'modalidad',
    FILTER_VALIDATE_INT
) ?: null;

$etapa = $_GET['etapa'] ?? '';
$estadoExpediente = $_GET['estado'] ?? '';

$etapasPermitidas = [
    '',
    'previa',
    'mg1',
    'mg2',
    'finalizado'
];

$estadosPermitidos = [
    '',
    'activo',
    'aprobado',
    'reprobado',
    'abandono',
    'retirado'
];

if (
    !in_array($etapa, $etapasPermitidas, true)
) {
    $etapa = '';
}

if (
    !in_array(
        $estadoExpediente,
        $estadosPermitidos,
        true
    )
) {
    $estadoExpediente = '';
}

$expedientes = $modeloReporte->listarGeneral(
    $idCohorte,
    $idModalidad,
    $etapa,
    $estadoExpediente
);

$cohortes = $modeloReporte->listarCohortes();
$modalidades = $modeloReporte->listarModalidades();

$resumenEtapas = [
    'previa' => 0,
    'mg1' => 0,
    'mg2' => 0,
    'finalizado' => 0
];

foreach ($expedientes as $expediente) {
    $etapaRegistro = $expediente['etapa_actual'];

    if (isset($resumenEtapas[$etapaRegistro])) {
        $resumenEtapas[$etapaRegistro]++;
    }
}

$puedeExportar = usuarioTienePermiso(
    'mg.reportes.exportar'
);

$tituloPagina = 'Reporte general';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reportes/general.php';