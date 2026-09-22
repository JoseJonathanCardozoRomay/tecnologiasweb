<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new UsuarioModel();
$roles = $modelo->listarRoles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasena = trim($_POST['contrasena'] ?? '');
    
    if (empty($_POST['id_rol']) || empty($_POST['nombre']) || empty($_POST['apellido']) || 
        empty($_POST['correo']) || empty($_POST['usuario']) || empty($contrasena)) {
        header('Location: index.php?accion=usuarios_crear&mensaje=campos_vacios');
        exit;
    }

    $datos = [
        'id_rol' => $_POST['id_rol'],
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'correo' => trim($_POST['correo']),
        'usuario' => trim($_POST['usuario']),
        'contrasena_hash' => password_hash($contrasena, PASSWORD_DEFAULT),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'estado' => $_POST['estado'] ?? 'activo'
    ];

    $resultado = $modelo->crear($datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header('Location: index.php?accion=usuarios_crear&mensaje=error&detalle=' . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=usuarios_listar&mensaje=registro_creado');
    exit;
}

require_once __DIR__ . '/../views/usuarios/crear.php';