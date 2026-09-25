<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La administración de periodos corresponde a coordinación
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloPeriodo = new PeriodoModel($pdo);

$busqueda = trim(
    $_GET['buscar'] ?? ''
);

// Obtenemos los periodos registrados
$periodos = $modeloPeriodo->listar(
    $busqueda
);

// Mensajes utilizados después de cada operación
$mensajes = [
    'creado' => 'El periodo fue registrado correctamente.',
    'actualizado' => 'El periodo fue actualizado correctamente.',
    'abierto' => 'El periodo fue abierto correctamente.',
    'cerrado' => 'El periodo fue cerrado correctamente.',
    'planificado' => 'El periodo quedó en estado planificado.',
    'no_encontrado' => 'No se encontró el periodo solicitado.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'error'],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Periodos de inscripción';
$rutaBase = '../';

require_once __DIR__ . '/../views/periodos/listar.php';