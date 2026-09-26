<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede eliminar materias
requerirRol(
    ['administrador'],
    '../index.php'
);

// La eliminación debe enviarse desde el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: materias_listar.php');
    exit;
}

$idMateria = filter_input(
    INPUT_POST,
    'id_materia',
    FILTER_VALIDATE_INT
);

if (!$idMateria) {
    header(
        'Location: materias_listar.php?estado=no_encontrada'
    );
    exit;
}

$modeloMateria = new MateriaModel($pdo);
$materia = $modeloMateria->buscarPorId($idMateria);

if (!$materia) {
    header(
        'Location: materias_listar.php?estado=no_encontrada'
    );
    exit;
}

// No eliminamos materias asignadas a tutores o tutorías
if ($modeloMateria->tieneRegistrosRelacionados($idMateria)) {
    header(
        'Location: materias_listar.php?estado=relacionada'
    );
    exit;
}

try {
    $modeloMateria->eliminar($idMateria);

    header(
        'Location: materias_listar.php?estado=eliminada'
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: materias_listar.php?estado=relacionada'
    );
    exit;
}