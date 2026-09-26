<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede gestionar las carreras
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloCarrera = new CarreraModel($pdo);

// Conservamos el texto escrito en el buscador
$busqueda = trim($_GET['buscar'] ?? '');

$carreras = $modeloCarrera->listar($busqueda);

// Preparamos los mensajes utilizados después de cada operación
$mensajes = [
    'creada' => 'La carrera fue registrada correctamente.',
    'actualizada' => 'La carrera fue actualizada correctamente.',
    'eliminada' => 'La carrera fue eliminada correctamente.',
    'relacionada' => 'La carrera no puede eliminarse porque tiene registros relacionados.',
    'no_encontrada' => 'No se encontró la carrera solicitada.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['relacionada', 'no_encontrada'],
    true
) ? 'danger' : 'success';

// Datos necesarios para los layouts
$tituloPagina = 'Carreras';
$rutaBase = '../';

require_once __DIR__ . '/../views/carreras/listar.php';