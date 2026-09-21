<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede eliminar carreras
requerirRol(
    ['administrador'],
    '../index.php'
);

// No permitimos ejecutar la eliminación mediante la dirección URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: carreras_listar.php');
    exit;
}

$idCarrera = filter_input(
    INPUT_POST,
    'id_carrera',
    FILTER_VALIDATE_INT
);

if (!$idCarrera) {
    header(
        'Location: carreras_listar.php?estado=no_encontrada'
    );
    exit;
}

$modeloCarrera = new CarreraModel($pdo);
$carrera = $modeloCarrera->buscarPorId($idCarrera);

if (!$carrera) {
    header(
        'Location: carreras_listar.php?estado=no_encontrada'
    );
    exit;
}

// Conservamos las carreras que tienen estudiantes o materias
if ($modeloCarrera->tieneRegistrosRelacionados($idCarrera)) {
    header(
        'Location: carreras_listar.php?estado=relacionada'
    );
    exit;
}

try {
    $modeloCarrera->eliminar($idCarrera);

    header(
        'Location: carreras_listar.php?estado=eliminada'
    );
    exit;
} catch (PDOException $e) {
    // Si aparece una relación nueva, evitamos mostrar un error técnico
    header(
        'Location: carreras_listar.php?estado=relacionada'
    );
    exit;
}