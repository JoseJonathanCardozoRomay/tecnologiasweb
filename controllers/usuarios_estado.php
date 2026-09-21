<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede cambiar estados
requerirRol(
    ['administrador'],
    '../index.php'
);

// El cambio debe enviarse desde el formulario del listado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios_listar.php');
    exit;
}

$idUsuario = filter_input(
    INPUT_POST,
    'id_usuario',
    FILTER_VALIDATE_INT
);

$nuevoEstado = $_POST['estado'] ?? '';

if (
    !$idUsuario
    || !in_array(
        $nuevoEstado,
        ['activo', 'inactivo'],
        true
    )
) {
    header(
        'Location: usuarios_listar.php?estado=no_encontrado'
    );
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();

// Impedimos que el administrador bloquee su sesión actual
if ((int) $usuarioSesion['id_usuario'] === $idUsuario) {
    header(
        'Location: usuarios_listar.php?estado=sesion_actual'
    );
    exit;
}

$modeloUsuario = new UsuarioModel($pdo);
$usuarioEncontrado = $modeloUsuario->buscarPorId($idUsuario);

if (!$usuarioEncontrado) {
    header(
        'Location: usuarios_listar.php?estado=no_encontrado'
    );
    exit;
}

try {
    $modeloUsuario->cambiarEstado(
        $idUsuario,
        $nuevoEstado
    );

    $mensajeEstado = $nuevoEstado === 'activo'
        ? 'activado'
        : 'inactivado';

    header(
        'Location: usuarios_listar.php?estado='
        . $mensajeEstado
    );
    exit;
} catch (PDOException $e) {
    header(
        'Location: usuarios_listar.php?estado=no_encontrado'
    );
    exit;
}