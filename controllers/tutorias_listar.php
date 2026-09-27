<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$tutoriaModel = new TutoriaModel($pdo);
$tutorias = $tutoriaModel->obtenerTodas();
$metricas = $tutoriaModel->obtenerMetricasGlobales();
$estadosConclusion = $tutoriaModel->obtenerEstadosConclusion();
$esAdministrador = ($_SESSION['rol'] ?? '') === 'administrador';
$esAuxiliar = ($_SESSION['rol'] ?? '') === 'auxiliar';

$filtroEstado = $p['estado'];
$tutorias = filtrarPorEstado($tutorias, $filtroEstado);
$tutorias = filtrarRegistros($tutorias, $p['q'], [
    'nombre_materia', 'estudiante_nombre', 'estudiante_apellido', 'tutor_nombre', 'tutor_apellido'
]);
$tutorias = ordenarRegistros($tutorias, $p['orden'], $p['dir'], [
    'fecha'     => 'fecha',
    'materia'   => 'nombre_materia',
    'estudiante'=> 'estudiante_apellido',
    'tutor'     => 'tutor_apellido',
    'estado'    => 'estado',
]);
[$tutorias, $pag] = paginarRegistros($tutorias, $p['pagina'], $p['por_pagina']);

$q = $p['q'];
$estadosFiltro = TutoriaModel::estadosPermitidos();
$ordenActual = $p['orden'];
$dirActual = $p['dir'];
$filtroPeriodo = '';
$periodos = [];

require_once __DIR__ . '/../views/tutorias/listar.php';