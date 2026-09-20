<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$materiaModel = new MateriaModel($pdo);
$carreraModel = new CarreraModel($pdo);
$ordenesPermitidos = ['id', 'nombre', 'carrera', 'tutores'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'nombre';
$dirActual = ($_GET['dir'] ?? '') === 'desc' ? 'desc' : 'asc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$id_carrera = isset($_GET['id_carrera']) && is_numeric($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();
$totalRegistros = $materiaModel->contar($q, $id_carrera);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$materias = $materiaModel->obtenerPaginadas($q, $id_carrera, $orden, $dir, $pag['por_pagina'], $pag['offset']);
$carreras = $carreraModel->obtenerTodasOrdenadas('nombre', 'asc');

require_once __DIR__ . '/../views/materias/listar.php';
