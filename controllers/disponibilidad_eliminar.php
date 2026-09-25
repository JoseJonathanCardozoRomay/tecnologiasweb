<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente los tutores pueden eliminar su disponibilidad
requerirRol(
    ['tutor'],
    '../index.php'
);

// No permitimos eliminaciones mediante enlaces GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: disponibilidad_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();

$modeloTutor = new TutorModel($pdo);
$modeloDisponibilidad = new DisponibilidadModel($pdo);

// Identificamos al tutor mediante su sesión
$tutor = $modeloTutor->buscarPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header(
        'Location: ../index.php?estado=sin_perfil_tutor'
    );
    exit;
}

$idDisponibilidad = filter_input(
    INPUT_POST,
    'id_disponibilidad',
    FILTER_VALIDATE_INT
);

if (!$idDisponibilidad) {
    header(
        'Location: disponibilidad_listar.php?estado=no_encontrado'
    );
    exit;
}

// Verificamos que el horario pertenezca al tutor autenticado
$disponibilidad = $modeloDisponibilidad->buscarPorId(
    $idDisponibilidad,
    (int) $tutor['id_tutor']
);

if (!$disponibilidad) {
    header(
        'Location: disponibilidad_listar.php?estado=no_encontrado'
    );
    exit;
}

try {
    $modeloDisponibilidad->eliminar(
        $idDisponibilidad,
        (int) $tutor['id_tutor']
    );

    header(
        'Location: disponibilidad_listar.php?estado=eliminado'
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: disponibilidad_listar.php?estado=error'
    );
    exit;
}