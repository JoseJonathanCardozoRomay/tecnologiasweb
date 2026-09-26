<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La gestión de tutores corresponde al administrador
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloTutor = new TutorModel($pdo);

$busqueda = trim($_GET['buscar'] ?? '');

// Obtenemos los tutores según la búsqueda realizada
$tutores = $modeloTutor->listar($busqueda);

// Mensajes utilizados después de cada operación
$mensajes = [
    'creado' => 'El perfil del tutor fue registrado correctamente.',
    'actualizado' => 'El perfil del tutor fue actualizado correctamente.',
    'eliminado' => 'El perfil del tutor fue eliminado correctamente.',
    'relacionado' => 'El tutor no puede eliminarse porque tiene tutorías registradas.',
    'no_encontrado' => 'No se encontró el tutor solicitado.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['relacionado', 'no_encontrado'],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Tutores';
$rutaBase = '../';

require_once __DIR__ . '/../views/tutores/listar.php';