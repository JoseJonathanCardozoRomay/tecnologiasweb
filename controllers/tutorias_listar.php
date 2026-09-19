<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$tutoriaModel = new TutoriaModel($pdo);

$rolSesion = $_SESSION['rol'] ?? '';
$idUsuario = $_SESSION['id_usuario'] ?? 0;

// Filtro por estado desde GET
$filtroEstado = $_GET['estado'] ?? null;
if (!in_array($filtroEstado, ['pendiente', 'confirmada', 'realizada', 'cancelada'])) {
    $filtroEstado = null;
}
$filtroPeriodo = isset($_GET['periodo']) && is_scalar($_GET['periodo']) ? trim((string) $_GET['periodo']) : '';
$ordenesPermitidos = ['fecha', 'materia', 'estudiante', 'tutor', 'estado'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'fecha';
$dirActual = ($_GET['dir'] ?? '') === 'asc' ? 'asc' : 'desc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();

$periodos = $tutoriaModel->obtenerPeriodosDisponibles();
$totalRegistros = $tutoriaModel->contar($q, $filtroEstado, $filtroPeriodo);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$tutorias = $tutoriaModel->obtenerPaginadas($q, $filtroEstado, $filtroPeriodo, $orden, $dir, $pag['por_pagina'], $pag['offset']);
$metricas = $tutoriaModel->obtenerMetricasGlobales($filtroPeriodo);

require_once __DIR__ . '/../views/tutorias/listar.php';
