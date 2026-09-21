<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La gestión de estudiantes corresponde al administrador
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloEstudiante = new EstudianteModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

$busqueda = trim($_GET['buscar'] ?? '');

$idCarrera = filter_input(
    INPUT_GET,
    'carrera',
    FILTER_VALIDATE_INT
);

if (!$idCarrera) {
    $idCarrera = null;
}

// Obtenemos los estudiantes según los filtros
$estudiantes = $modeloEstudiante->listar(
    $busqueda,
    $idCarrera
);

$carreras = $modeloCarrera->listar();

// Mensajes utilizados después de cada operación
$mensajes = [
    'creado' => 'El perfil del estudiante fue registrado correctamente.',
    'actualizado' => 'El perfil del estudiante fue actualizado correctamente.',
    'eliminado' => 'El perfil del estudiante fue eliminado correctamente.',
    'relacionado' => 'El estudiante no puede eliminarse porque tiene tutorías registradas.',
    'no_encontrado' => 'No se encontró el estudiante solicitado.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['relacionado', 'no_encontrado'],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Estudiantes';
$rutaBase = '../';

require_once __DIR__ . '/../views/estudiantes/listar.php';