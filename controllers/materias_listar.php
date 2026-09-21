<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La gestión de materias corresponde al administrador
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloMateria = new MateriaModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

$busqueda = trim($_GET['buscar'] ?? '');

$idCarrera = filter_input(
    INPUT_GET,
    'carrera',
    FILTER_VALIDATE_INT
);

// Si no se seleccionó una carrera, no aplicamos ese filtro
if (!$idCarrera) {
    $idCarrera = null;
}

$materias = $modeloMateria->listar(
    $busqueda,
    $idCarrera
);

// Cargamos las carreras disponibles para el filtro
$carreras = $modeloCarrera->listar();

// Mensajes utilizados después de cada operación
$mensajes = [
    'creada' => 'La materia fue registrada correctamente.',
    'actualizada' => 'La materia fue actualizada correctamente.',
    'eliminada' => 'La materia fue eliminada correctamente.',
    'relacionada' => 'La materia no puede eliminarse porque tiene registros relacionados.',
    'no_encontrada' => 'No se encontró la materia solicitada.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['relacionada', 'no_encontrada'],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Materias';
$rutaBase = '../';

require_once __DIR__ . '/../views/materias/listar.php';