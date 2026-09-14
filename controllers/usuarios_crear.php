<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_rol'   => $_POST['id_rol'] ?? '',
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? ''),
        'usuario'  => trim($_POST['usuario'] ?? ''),
        'clave'    => $_POST['clave'] ?? '',
    ];

    if (in_array('', [$datos['nombre'], $datos['apellido'], $datos['correo'], $datos['usuario'], $datos['clave']], true)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no tiene un formato válido.";
    }
    if (strlen($datos['clave']) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if (empty($errores)) {
        try {
            $usuarioModel->crear($datos);
            header("Location: usuarios_listar.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "No se pudo registrar: el correo o el usuario ya existen.";
        }
    }
}

$roles = $rolModel->obtenerTodos();
require_once __DIR__ . '/../views/usuarios/crear.php';