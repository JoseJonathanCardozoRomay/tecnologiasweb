<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new UsuarioModel();
$roles = $modelo->listarRoles();
$id_usuario = $_GET['id'] ?? 0;
$usuario = $modelo->obtenerPorId($id_usuario);

if (!$usuario) {
    header('Location: index.php?accion=usuarios_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id_rol']) || empty($_POST['nombre']) || empty($_POST['apellido']) || 
        empty($_POST['correo']) || empty($_POST['usuario'])) {
        header("Location: index.php?accion=usuarios_editar&id=$id_usuario&mensaje=campos_vacios");
        exit;
    }

    $datos = [
        'id_rol' => $_POST['id_rol'],
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'correo' => trim($_POST['correo']),
        'usuario' => trim($_POST['usuario']),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'estado' => $_POST['estado'] ?? 'activo'
    ];

    // Solo actualizar contraseña si se escribió una nueva
    if (!empty($_POST['contrasena'])) {
        $datos['contrasena_hash'] = password_hash(trim($_POST['contrasena']), PASSWORD_DEFAULT);
    }

    $resultado = $modelo->actualizar($id_usuario, $datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=usuarios_editar&id=$id_usuario&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=usuarios_listar&mensaje=registro_actualizado');
    exit;
}

require_once __DIR__ . '/../views/usuarios/editar.php';