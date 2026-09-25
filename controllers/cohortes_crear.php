<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.cohortes.crear',
    '../index.php'
);

$modeloCohorte = new CohorteMgModel($pdo);

$codigo = '';
$nombre = '';
$fechaInicio = '';
$fechaFin = '';
$error = '';

// Comprueba que una fecha tenga el formato utilizado por MySQL
$esFechaValida = static function (
    string $fecha
): bool {
    $fechaConvertida = DateTime::createFromFormat(
        '!Y-m-d',
        $fecha
    );

    return $fechaConvertida !== false
        && $fechaConvertida->format('Y-m-d') === $fecha;
};

// Procesamos los datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoRecibido = $_POST['codigo'] ?? '';
    $nombreRecibido = $_POST['nombre'] ?? '';
    $inicioRecibido = $_POST['fecha_inicio'] ?? '';
    $finRecibido = $_POST['fecha_fin'] ?? '';

    $codigo = is_string($codigoRecibido)
        ? strtoupper(trim($codigoRecibido))
        : '';

    $nombre = is_string($nombreRecibido)
        ? trim($nombreRecibido)
        : '';

    $fechaInicio = is_string($inicioRecibido)
        ? trim($inicioRecibido)
        : '';

    $fechaFin = is_string($finRecibido)
        ? trim($finRecibido)
        : '';

    if (!validarTokenCsrf()) {
        http_response_code(403);

        $error = 'La solicitud no superó la validación de seguridad.';
    } elseif (
        mb_strlen($codigo) < 3
        || mb_strlen($codigo) > 30
    ) {
        $error = 'El código debe contener entre 3 y 30 caracteres.';
    } elseif (
        !preg_match(
            '/^[A-Z0-9_-]+$/',
            $codigo
        )
    ) {
        $error = 'El código solo puede contener letras, números, guiones y guiones bajos.';
    } elseif (
        mb_strlen($nombre) < 3
        || mb_strlen($nombre) > 120
    ) {
        $error = 'El nombre debe contener entre 3 y 120 caracteres.';
    } elseif (!$esFechaValida($fechaInicio)) {
        $error = 'Selecciona una fecha de inicio válida.';
    } elseif (
        $fechaFin !== ''
        && !$esFechaValida($fechaFin)
    ) {
        $error = 'Selecciona una fecha de finalización válida.';
    } elseif (
        $fechaFin !== ''
        && $fechaFin < $fechaInicio
    ) {
        $error = 'La fecha de finalización no puede ser anterior a la fecha de inicio.';
    } elseif ($modeloCohorte->existeCodigo($codigo)) {
        $error = 'Ya existe una cohorte con ese código.';
    } else {
        try {
            $modeloCohorte->crear(
                $codigo,
                $nombre,
                $fechaInicio,
                $fechaFin !== ''
                    ? $fechaFin
                    : null
            );

            header(
                'Location: cohortes_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar la cohorte.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nueva cohorte';
$rutaBase = '../';

require_once __DIR__
    . '/../views/cohortes/crear.php';