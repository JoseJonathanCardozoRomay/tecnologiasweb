<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// La administración de usuarios corresponde al administrador
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloUsuario = new UsuarioModel($pdo);

$busqueda = trim($_GET['buscar'] ?? '');

$idRol = filter_input(
    INPUT_GET,
    'rol',
    FILTER_VALIDATE_INT
);

if (!$idRol) {
    $idRol = null;
}

$estadoFiltro = $_GET['estado_usuario'] ?? '';

if (!in_array(
    $estadoFiltro,
    ['activo', 'inactivo'],
    true
)) {
    $estadoFiltro = '';
}

// Obtenemos usuarios según los filtros seleccionados
$usuarios = $modeloUsuario->listar(
    $busqueda,
    $idRol,
    $estadoFiltro
);

$roles = $modeloUsuario->listarRoles();

// Mensajes mostrados después de cada operación
$mensajes = [
    'creado' => 'El usuario fue registrado correctamente.',
    'actualizado' => 'El usuario fue actualizado correctamente.',
    'activado' => 'El usuario fue activado correctamente.',
    'inactivado' => 'El usuario fue inactivado correctamente.',
    'no_encontrado' => 'No se encontró el usuario solicitado.',
    'sesion_actual' => 'No puedes inactivar tu propia cuenta mientras la estás utilizando.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'sesion_actual'],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Usuarios';
$rutaBase = '../';

require_once __DIR__ . '/../views/usuarios/listar.php';