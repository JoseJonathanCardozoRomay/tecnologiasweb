<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';

// Solamente el administrador puede eliminar perfiles
requerirRol(
    ['administrador'],
    '../index.php'
);

// Esta operación únicamente debe recibirse mediante POST
// Protegemos también los formularios de los CRUD existentes.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validarTokenCsrf()) {
    http_response_code(403);
    exit('Formulario vencido. Recarga la página e inténtalo de nuevo.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tutores_listar.php');
    exit;
}

$idTutor = filter_input(
    INPUT_POST,
    'id_tutor',
    FILTER_VALIDATE_INT
);

if (!$idTutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

$modeloTutor = new TutorModel($pdo);
$tutor = $modeloTutor->buscarPorId($idTutor);

if (!$tutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

// Conservamos perfiles que ya estén relacionados con tutorías
if ($modeloTutor->tieneTutorias($idTutor)) {
    header(
        'Location: tutores_listar.php?estado=relacionado'
    );
    exit;
}

try {
    $modeloTutor->eliminar($idTutor);

    header(
        'Location: tutores_listar.php?estado=eliminado'
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: tutores_listar.php?estado=relacionado'
    );
    exit;
}