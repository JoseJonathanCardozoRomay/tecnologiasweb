<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();
$cohortes = filtrarRegistros($cohortes, $p['q'], ['nombre_periodo', 'observaciones']);
$cohortes = filtrarPorEstado($cohortes, $p['estado'], 'estado');
$cohortes = ordenarRegistros($cohortes, $p['orden'], $p['dir'], [
    'id' => 'id_cohorte_mg',
    'periodo' => 'nombre_periodo',
    'inicio' => 'fecha_inicio',
    'expedientes' => 'total_expedientes',
]);
[$cohortes, $pag] = paginarRegistros($cohortes, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$estado = $p['estado'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/mg/cohortes/listar.php';