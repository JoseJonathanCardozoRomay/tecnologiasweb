<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$materiaModel = new MateriaModel($pdo);
$ordenesPermitidos = ['id', 'nombre', 'carrera', 'tutores'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'nombre';
$dirActual = ($_GET['dir'] ?? '') === 'desc' ? 'desc' : 'asc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();
$totalRegistros = $materiaModel->contar($q);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$materias = $materiaModel->obtenerPaginadas($q, $orden, $dir, $pag['por_pagina'], $pag['offset']);

require_once __DIR__ . '/../views/materias/listar.php';
