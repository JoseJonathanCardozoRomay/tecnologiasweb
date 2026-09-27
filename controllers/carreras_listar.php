<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$carreraModel = new CarreraModel($pdo);
$carreras = $carreraModel->obtenerTodas();
$carreras = filtrarRegistros($carreras, $p['q'], ['nombre_carrera']);
$carreras = ordenarRegistros($carreras, $p['orden'], $p['dir'], [
    'id'          => 'id_carrera',
    'nombre'      => 'nombre_carrera',
    'materias'    => 'total_materias',
    'estudiantes' => 'total_estudiantes',
]);
[$carreras, $pag] = paginarRegistros($carreras, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/carreras/listar.php';