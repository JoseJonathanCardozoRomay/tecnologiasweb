<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/ImportacionMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.importaciones.ver',
    '../index.php'
);

$idImportacion = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idImportacion) {
    header(
        'Location: importaciones_listar.php'
    );
    exit;
}

$modeloImportacion = new ImportacionMgModel($pdo);

$importaciones = $modeloImportacion->listar();
$importacion = null;

foreach ($importaciones as $registro) {
    if (
        (int) $registro['id_importacion']
        === $idImportacion
    ) {
        $importacion = $registro;
        break;
    }
}

if (!$importacion) {
    header(
        'Location: importaciones_listar.php'
    );
    exit;
}

$detalles = $modeloImportacion->listarDetalle(
    $idImportacion
);

$tituloPagina = 'Detalle de importación';
$rutaBase = '../';

require_once __DIR__
    . '/../views/importaciones/detalle.php';