<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DocumentoMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso('mg.documentos.ver', '../index.php');

$busqueda = trim((string) ($_GET['q'] ?? ''));
$busqueda = mb_substr($busqueda, 0, 100);

$modeloDocumento = new DocumentoMgModel($pdo);
$documentos = $modeloDocumento->listarGenerados($busqueda);

$tituloPagina = 'Documentos generados';
$rutaBase = '../';

require_once __DIR__ . '/../views/documentos/listar.php';