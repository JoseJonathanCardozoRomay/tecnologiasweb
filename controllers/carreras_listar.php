<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$carreraModel = new CarreraModel($pdo);
$ordenesPermitidos = ['id', 'nombre', 'materias', 'estudiantes'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'nombre';
$dirActual = ($_GET['dir'] ?? '') === 'desc' ? 'desc' : 'asc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();
$totalRegistros = $carreraModel->contar($q);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$carreras = $carreraModel->obtenerPaginadas($q, $orden, $dir, $pag['por_pagina'], $pag['offset']);

require_once __DIR__ . '/../views/carreras/listar.php';
