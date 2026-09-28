<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/ImportacionMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.importaciones.ver',
    '../index.php'
);

$modeloImportacion = new ImportacionMgModel($pdo);

$importaciones = $modeloImportacion->listar();

$tituloPagina = 'Historial de importaciones';
$rutaBase = '../';

require_once __DIR__
    . '/../views/importaciones/listar.php';