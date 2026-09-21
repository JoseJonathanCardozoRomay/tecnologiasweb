<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede modificar perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloEstudiante = new EstudianteModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

// El identificador puede llegar por GET o desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEstudiante = filter_input(
        INPUT_POST,
        'id_estudiante',
        FILTER_VALIDATE_INT
    );
} else {
    $idEstudiante = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idEstudiante) {
    header(
        'Location: estudiantes_listar.php?estado=no_encontrado'
    );
    exit;
}

$estudiante = $modeloEstudiante->buscarPorId($idEstudiante);

if (!$estudiante) {
    header(
        'Location: estudiantes_listar.php?estado=no_encontrado'
    );
    exit;
}

$carreras = $modeloCarrera->listar();

$idCarrera = (int) $estudiante['id_carrera'];
$semestre = (int) $estudiante['semestre'];

$registroUniversitario = $estudiante['registro_universitario']
    ?? '';

$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCarrera = filter_input(
        INPUT_POST,
        'id_carrera',
        FILTER_VALIDATE_INT
    );

    $semestre = filter_input(
        INPUT_POST,
        'semestre',
        FILTER_VALIDATE_INT
    );

    $registroUniversitario = trim(
        $_POST['registro_universitario'] ?? ''
    );

    if (
        !$idCarrera
        || !$modeloCarrera->buscarPorId($idCarrera)
    ) {
        $error = 'Selecciona una carrera válida.';
    } elseif (
        !$semestre
        || $semestre < 1
        || $semestre > 10
    ) {
        $error = 'El semestre debe estar entre 1 y 10.';
    } elseif (
        mb_strlen($registroUniversitario) > 30
    ) {
        $error = 'El registro universitario no puede superar los 30 caracteres.';
    } elseif (
        $registroUniversitario !== ''
        && !preg_match(
            '/^[a-zA-Z0-9-]+$/',
            $registroUniversitario
        )
    ) {
        $error = 'El registro universitario solo puede contener letras, números y guiones.';
    } elseif (
        $modeloEstudiante->existeRegistro(
            $registroUniversitario,
            $idEstudiante
        )
    ) {
        $error = 'El registro universitario ya está asignado a otro estudiante.';
    } else {
        try {
            $modeloEstudiante->actualizar(
                $idEstudiante,
                $idCarrera,
                $semestre,
                $registroUniversitario !== ''
                    ? $registroUniversitario
                    : null
            );

            header(
                'Location: estudiantes_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar el perfil del estudiante.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar estudiante';
$rutaBase = '../';

require_once __DIR__ . '/../views/estudiantes/editar.php';