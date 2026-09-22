<?php
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
        'telefono' => trim($_POST['telefono'] ?? ''),
        'estado' => $_POST['estado'] ?? 'activo'
    ];

    if ($datos['id_rol'] <= 0 || empty($datos['nombre']) || empty($datos['apellido']) || 
        empty($datos['correo']) || empty($datos['usuario']) || empty($datos['contrasena'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=usuarios_listar');
            exit;
        }
        $error = 'Error al crear el usuario — Verifica que el usuario y correo no estén repetidos';
    }
}

require_once __DIR__ . '/../views/usuarios/crear.php';