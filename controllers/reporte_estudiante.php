<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReporteMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.reportes.ver',
    '../index.php'
);

$idExpediente = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idExpediente) {
    header(
        'Location: expedientes_listar.php'
    );
    exit;
}

$modeloReporte = new ReporteMgModel($pdo);

$expediente = $modeloReporte->buscarExpediente(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: expedientes_listar.php'
    );
    exit;
}

$tutores = $modeloReporte->listarTutores(
    $idExpediente
);

$tribunales = $modeloReporte->listarTribunales(
    $idExpediente
);

$defensas = $modeloReporte->listarDefensas(
    $idExpediente
);

$seguimiento = $modeloReporte->obtenerSeguimiento(
    $idExpediente
);

$tituloPagina = 'Reporte del estudiante';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reportes/estudiante.php';