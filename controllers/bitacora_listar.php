<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.bitacora.ver',
    '../index.php'
);

$modeloBitacora = new BitacoraModel($pdo);

$filtrosDisponibles = $modeloBitacora
    ->obtenerFiltros();

$accion = trim(
    $_GET['accion'] ?? ''
);

$tabla = trim(
    $_GET['tabla'] ?? ''
);

$idUsuario = filter_input(
    INPUT_GET,
    'usuario',
    FILTER_VALIDATE_INT
) ?: null;

$fechaDesde = trim(
    $_GET['fecha_desde'] ?? ''
);

$fechaHasta = trim(
    $_GET['fecha_hasta'] ?? ''
);

if (
    $accion !== ''
    && !in_array(
        $accion,
        $filtrosDisponibles['acciones'],
        true
    )
) {
    $accion = '';
}

if (
    $tabla !== ''
    && !in_array(
        $tabla,
        $filtrosDisponibles['tablas'],
        true
    )
) {
    $tabla = '';
}

$validarFecha = function (string $fecha): string {
    if ($fecha === '') {
        return '';
    }

    $objetoFecha = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    return $objetoFecha
        && $objetoFecha->format('Y-m-d') === $fecha
            ? $fecha
            : '';
};

$fechaDesde = $validarFecha($fechaDesde);
$fechaHasta = $validarFecha($fechaHasta);

$registros = $modeloBitacora->listar(
    $accion,
    $tabla,
    $idUsuario,
    $fechaDesde,
    $fechaHasta
);

$tituloPagina = 'Bitácora de auditoría';
$rutaBase = '../';

require_once __DIR__
    . '/../views/bitacora/listar.php';