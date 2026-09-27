<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$tutorModel = new TutorModel($pdo);
$tutores = $tutorModel->obtenerTodos();
$tutores = filtrarRegistros($tutores, $p['q'], ['nombre', 'apellido', 'correo', 'especialidad']);
$tutores = ordenarRegistros($tutores, $p['orden'], $p['dir'], [
    'nombre'       => 'apellido',
    'especialidad' => 'especialidad',
]);
[$tutores, $pag] = paginarRegistros($tutores, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/tutores/listar.php';