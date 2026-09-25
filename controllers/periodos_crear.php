<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede registrar periodos
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloPeriodo = new PeriodoModel($pdo);

$estadosPermitidos = [
    'planificado',
    'abierto',
    'cerrado'
];

$codigo = '';
$nombre = '';
$fechaInicio = '';
$fechaFin = '';
$estadoPeriodo = 'planificado';
$error = '';

// Comprueba que una fecha tenga el formato esperado
$fechaValida = static function (string $fecha): bool {
    $fechaConvertida = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    return (
        $fechaConvertida !== false
        && $fechaConvertida->format('Y-m-d') === $fecha
    );
};

// Procesamos los datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = strtoupper(
        trim($_POST['codigo'] ?? '')
    );

    $nombre = trim(
        $_POST['nombre'] ?? ''
    );

    $fechaInicio = trim(
        $_POST['fecha_inicio'] ?? ''
    );

    $fechaFin = trim(
        $_POST['fecha_fin'] ?? ''
    );

    $estadoPeriodo = trim(
        $_POST['estado'] ?? ''
    );

    if (
        $codigo === ''
        || mb_strlen($codigo) < 3
        || mb_strlen($codigo) > 30
    ) {
        $error = 'El código debe contener entre 3 y 30 caracteres.';
    } elseif (
        !preg_match(
            '/^[A-Z0-9-]+$/',
            $codigo
        )
    ) {
        $error = 'El código solo puede contener letras, números y guiones.';
    } elseif (
        $nombre === ''
        || mb_strlen($nombre) < 3
        || mb_strlen($nombre) > 120
    ) {
        $error = 'El nombre debe contener entre 3 y 120 caracteres.';
    } elseif (
        !preg_match(
            '/^[\p{L}\p{N}\s\/-]+$/u',
            $nombre
        )
    ) {
        $error = 'El nombre contiene caracteres no permitidos.';
    } elseif (
        !$fechaValida($fechaInicio)
        || !$fechaValida($fechaFin)
    ) {
        $error = 'Selecciona fechas válidas.';
    } elseif ($fechaFin < $fechaInicio) {
        $error = 'La fecha final no puede ser anterior a la fecha inicial.';
    } elseif (
        !in_array(
            $estadoPeriodo,
            $estadosPermitidos,
            true
        )
    ) {
        $error = 'Selecciona un estado válido.';
    } elseif ($modeloPeriodo->existeCodigo($codigo)) {
        $error = 'Ya existe un periodo con ese código.';
    } else {
        try {
            $modeloPeriodo->crear(
                $codigo,
                $nombre,
                $fechaInicio,
                $fechaFin,
                $estadoPeriodo
            );

            header(
                'Location: periodos_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar el periodo.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo periodo';
$rutaBase = '../';

require_once __DIR__ . '/../views/periodos/crear.php';