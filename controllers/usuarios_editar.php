<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);

$id = $_GET['id'] ?? $_POST['id_usuario'] ?? null;
if (!$id) {
    header("Location: usuarios_listar.php");
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_rol'   => $_POST['id_rol'] ?? '',
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? ''),
        'usuario'  => trim($_POST['usuario'] ?? ''),
        'estado'   => $_POST['estado'] ?? 'activo',
    ];

    if (in_array('', [$datos['nombre'], $datos['apellido'], $datos['correo'], $datos['usuario']], true)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no tiene un formato válido.";
    }

    if (empty($errores)) {
        try {
            $usuarioModel->actualizar($id, $datos);
            header("Location: usuarios_listar.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "No se pudo actualizar: el correo o el usuario ya están en uso.";
        }
    }
}

$usuario_actual = $usuarioModel->obtenerPorId($id);
if (!$usuario_actual) {
    header("Location: usuarios_listar.php");
    exit;
}

$roles = $rolModel->obtenerTodos();
require_once __DIR__ . '/../views/usuarios/editar.php';