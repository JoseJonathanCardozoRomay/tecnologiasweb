<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede crear perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloEstudiante = new EstudianteModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

$usuariosDisponibles = $modeloEstudiante
    ->listarUsuariosDisponibles();

$carreras = $modeloCarrera->listar();

$idUsuario = null;
$idCarrera = null;
$semestre = null;
$registroUniversitario = '';
$error = '';

// Procesamos los datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = filter_input(
        INPUT_POST,
        'id_usuario',
        FILTER_VALIDATE_INT
    );

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

    $usuariosValidos = array_map(
        fn(array $usuario): int => (int) $usuario['id_usuario'],
        $usuariosDisponibles
    );

    if (
        !$idUsuario
        || !in_array($idUsuario, $usuariosValidos, true)
    ) {
        $error = 'Selecciona una cuenta de estudiante válida.';
    } elseif (
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
            $registroUniversitario
        )
    ) {
        $error = 'El registro universitario ya está asignado a otro estudiante.';
    } else {
        try {
            $modeloEstudiante->crear(
                $idUsuario,
                $idCarrera,
                $semestre,
                $registroUniversitario !== ''
                    ? $registroUniversitario
                    : null
            );

            header(
                'Location: estudiantes_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar el perfil del estudiante.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo estudiante';
$rutaBase = '../';

require_once __DIR__ . '/../views/estudiantes/crear.php';