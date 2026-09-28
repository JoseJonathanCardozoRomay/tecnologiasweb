<?php
/**
 * Crear Usuario — Corregido con mensajes claros
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../models/UsuarioModel.php';
$modelo = new UsuarioModel();
$roles = $modelo->listarRoles();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_rol' => (int)($_POST['id_rol'] ?? 0),
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'correo' => trim($_POST['correo'] ?? ''),
        'usuario' => trim($_POST['usuario'] ?? ''),
        'contrasena' => $_POST['contrasena'] ?? '',
        'telefono' => trim($_POST['telefono'] ?? '')
    ];

    // Validación paso a paso con mensaje claro
    if ($datos['id_rol'] <= 0) {
        $error = '⚠️ Debes seleccionar un Rol';
    } elseif (empty($datos['nombre'])) {
        $error = '⚠️ Escribe el Nombre';
    } elseif (empty($datos['apellido'])) {
        $error = '⚠️ Escribe el Apellido';
    } elseif (empty($datos['correo'])) {
        $error = '⚠️ Escribe el Correo';
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $error = '⚠️ El formato del correo no es válido';
    } elseif (empty($datos['usuario'])) {
        $error = '⚠️ Escribe el Usuario';
    } elseif (empty($datos['contrasena'])) {
        $error = '⚠️ Escribe la Contraseña';
    } elseif ($modelo->existeUsuario($datos['usuario'])) {
        $error = "⚠️ El usuario '{$datos['usuario']}' ya existe, elige otro";
    } elseif ($modelo->existeCorreo($datos['correo'])) {
        $error = "⚠️ El correo '{$datos['correo']}' ya está registrado";
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=usuarios_listar');
            exit;
        }
        $error = '❌ Error al guardar en la base de datos';
    }
}

require_once __DIR__ . '/../views/usuarios/crear.php';