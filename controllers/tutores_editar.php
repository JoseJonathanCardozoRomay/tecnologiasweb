<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede modificar perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloTutor = new TutorModel($pdo);

// El identificador puede llegar por GET o desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTutor = filter_input(
        INPUT_POST,
        'id_tutor',
        FILTER_VALIDATE_INT
    );
} else {
    $idTutor = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idTutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

$tutor = $modeloTutor->buscarPorId($idTutor);

if (!$tutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

$especialidad = $tutor['especialidad'] ?? '';
$biografia = $tutor['biografia'] ?? '';
$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $especialidad = trim(
        $_POST['especialidad'] ?? ''
    );

    $biografia = trim(
        $_POST['biografia'] ?? ''
    );

    if (
        mb_strlen($especialidad) > 150
    ) {
        $error = 'La especialidad no puede superar los 150 caracteres.';
    } elseif (
        mb_strlen($biografia) > 2000
    ) {
        $error = 'La biografía no puede superar los 2000 caracteres.';
    } else {
        try {
            $modeloTutor->actualizar(
                $idTutor,
                $especialidad !== ''
                    ? $especialidad
                    : null,
                $biografia !== ''
                    ? $biografia
                    : null
            );

            header(
                'Location: tutores_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar el perfil del tutor.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar tutor';
$rutaBase = '../';

require_once __DIR__ . '/../views/tutores/editar.php';