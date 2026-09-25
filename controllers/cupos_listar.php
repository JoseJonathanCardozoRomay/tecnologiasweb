<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CupoModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La configuración de cupos corresponde a coordinación
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloCupo = new CupoModel($pdo);
$modeloPeriodo = new PeriodoModel($pdo);

$idPeriodo = filter_input(
    INPUT_GET,
    'periodo',
    FILTER_VALIDATE_INT
);

// No se puede administrar cupos sin un periodo válido
if (!$idPeriodo) {
    header(
        'Location: periodos_listar.php?estado=no_encontrado'
    );
    exit;
}

$periodo = $modeloPeriodo->buscarPorId($idPeriodo);

if (!$periodo) {
    header(
        'Location: periodos_listar.php?estado=no_encontrado'
    );
    exit;
}

// Limitamos la búsqueda para evitar entradas demasiado extensas
$busqueda = trim($_GET['buscar'] ?? '');
$busqueda = mb_substr($busqueda, 0, 100);

$tutores = $modeloCupo->listarPorPeriodo(
    $idPeriodo,
    $busqueda
);

$resumen = $modeloCupo->obtenerResumen($idPeriodo);

// Mensajes mostrados después de guardar una configuración
$mensajes = [
    'guardado' => 'La configuración de cupos fue actualizada correctamente.',
    'datos_invalidos' => 'Los datos enviados no son válidos.',
    'tutor_no_encontrado' => 'No se encontró el tutor seleccionado.',
    'periodo_no_encontrado' => 'No se encontró el periodo solicitado.',
    'cupo_inferior' => 'El cupo máximo no puede ser menor que los procesos actualmente asignados.',
    'error' => 'No fue posible actualizar la configuración de cupos.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'datos_invalidos',
        'tutor_no_encontrado',
        'periodo_no_encontrado',
        'cupo_inferior',
        'error'
    ],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Cupos por tutor';
$rutaBase = '../';

require_once __DIR__ . '/../views/cupos/listar.php';