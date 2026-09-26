<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede modificar carreras
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloCarrera = new CarreraModel($pdo);

// El identificador puede llegar por GET o mediante el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCarrera = filter_input(
        INPUT_POST,
        'id_carrera',
        FILTER_VALIDATE_INT
    );
} else {
    $idCarrera = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

// Regresamos al listado si el identificador no es válido
if (!$idCarrera) {
    header(
        'Location: carreras_listar.php?estado=no_encontrada'
    );
    exit;
}

$carrera = $modeloCarrera->buscarPorId($idCarrera);

if (!$carrera) {
    header(
        'Location: carreras_listar.php?estado=no_encontrada'
    );
    exit;
}

$nombreCarrera = $carrera['nombre_carrera'];
$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCarrera = trim($_POST['nombre_carrera'] ?? '');

    if ($nombreCarrera === '') {
        $error = 'Ingresa el nombre de la carrera.';
    } elseif (mb_strlen($nombreCarrera) < 3) {
        $error = 'El nombre debe tener al menos 3 caracteres.';
    } elseif (mb_strlen($nombreCarrera) > 150) {
        $error = 'El nombre no puede superar los 150 caracteres.';
    } elseif (
        $modeloCarrera->existeNombre(
            $nombreCarrera,
            $idCarrera
        )
    ) {
        $error = 'Ya existe otra carrera registrada con ese nombre.';
    } else {
        try {
            $modeloCarrera->actualizar(
                $idCarrera,
                $nombreCarrera
            );

            header(
                'Location: carreras_listar.php?estado=actualizada'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar la carrera.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar carrera';
$rutaBase = '../';

require_once __DIR__ . '/../views/carreras/editar.php';