<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);
$bitacora = new BitacoraModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $datos = [
        'id_rol'   => $_POST['id_rol'] ?? '',
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? ''),
        'usuario'  => trim($_POST['usuario'] ?? ''),
        'clave'    => trim($_POST['clave'] ?? ''),
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
            $id_nuevo = $usuarioModel->crear($datos);
            $bitacora->registrar((int) $_SESSION['id_usuario'], (int) $id_nuevo, 'crear', [
                'rol'     => $datos['id_rol'],
                'nombre'  => $datos['nombre'] . ' ' . $datos['apellido'],
                'correo'  => $datos['correo'],
                'usuario' => $datos['usuario'],
            ]);
            flash_set('success', 'Usuario registrado correctamente.');
            header('Location: usuarios_listar.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "No se pudo registrar: el correo o el usuario ya existen.";
        }
    }
}

$roles = $rolModel->obtenerTodos();
require_once __DIR__ . '/../views/usuarios/crear.php';