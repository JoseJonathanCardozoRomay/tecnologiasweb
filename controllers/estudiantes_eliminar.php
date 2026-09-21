<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede eliminar perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

// La eliminación debe enviarse desde el listado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: estudiantes_listar.php');
    exit;
}

$idEstudiante = filter_input(
    INPUT_POST,
    'id_estudiante',
    FILTER_VALIDATE_INT
);

if (!$idEstudiante) {
    header(
        'Location: estudiantes_listar.php?estado=no_encontrado'
    );
    exit;
}

$modeloEstudiante = new EstudianteModel($pdo);
$estudiante = $modeloEstudiante->buscarPorId($idEstudiante);

if (!$estudiante) {
    header(
        'Location: estudiantes_listar.php?estado=no_encontrado'
    );
    exit;
}

// Conservamos perfiles que ya tienen tutorías relacionadas
if ($modeloEstudiante->tieneTutorias($idEstudiante)) {
    header(
        'Location: estudiantes_listar.php?estado=relacionado'
    );
    exit;
}

try {
    $modeloEstudiante->eliminar($idEstudiante);

    header(
        'Location: estudiantes_listar.php?estado=eliminado'
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: estudiantes_listar.php?estado=relacionado'
    );
    exit;
}