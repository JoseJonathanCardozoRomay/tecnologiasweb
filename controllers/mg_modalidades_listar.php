<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$p = parametrosListado();

$modalidadModel = new MgModalidadModel($pdo);
$modalidades = $modalidadModel->obtenerTodas();
$modalidades = filtrarRegistros($modalidades, $p['q'], ['nombre', 'flujo']);
$modalidades = filtrarPorEstado($modalidades, $p['estado'], 'activa');
$modalidades = ordenarRegistros($modalidades, $p['orden'], $p['dir'], [
    'id' => 'id_modalidad_grado',
    'nombre' => 'nombre',
    'flujo' => 'flujo',
    'expedientes' => 'total_expedientes',
]);
[$modalidades, $pag] = paginarRegistros($modalidades, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$estado = $p['estado'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];
$flujos = mgFlujos();

require_once __DIR__ . '/../views/mg/modalidades/listar.php';