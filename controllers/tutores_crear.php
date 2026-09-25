<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede crear perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloTutor = new TutorModel($pdo);

$usuariosDisponibles = $modeloTutor
    ->listarUsuariosDisponibles();

$idUsuario = null;
$especialidad = '';
$biografia = '';
$error = '';

// Procesamos los datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = filter_input(
        INPUT_POST,
        'id_usuario',
        FILTER_VALIDATE_INT
    );

    $especialidad = trim(
        $_POST['especialidad'] ?? ''
    );

    $biografia = trim(
        $_POST['biografia'] ?? ''
    );

    $usuariosValidos = array_map(
        fn(array $usuario): int => (int) $usuario['id_usuario'],
        $usuariosDisponibles
    );

    if (
        !$idUsuario
        || !in_array($idUsuario, $usuariosValidos, true)
    ) {
        $error = 'Selecciona una cuenta de tutor válida.';
    } elseif (
        mb_strlen($especialidad) > 150
    ) {
        $error = 'La especialidad no puede superar los 150 caracteres.';
    } elseif (
        mb_strlen($biografia) > 2000
    ) {
        $error = 'La biografía no puede superar los 2000 caracteres.';
    } else {
        try {
            $modeloTutor->crear(
                $idUsuario,
                $especialidad !== ''
                    ? $especialidad
                    : null,
                $biografia !== ''
                    ? $biografia
                    : null
            );

            header(
                'Location: tutores_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar el perfil del tutor.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo tutor';
$rutaBase = '../';

require_once __DIR__ . '/../views/tutores/crear.php';