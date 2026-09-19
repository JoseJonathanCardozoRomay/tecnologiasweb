<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RolModel.php';
require_once __DIR__ . '/../includes/validador.php';

$usuarioModel = new UsuarioModel($pdo);
$rolModel = new RolModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_rol'   => (int) ($_POST['id_rol'] ?? 0),
        'nombre'   => normalizarTexto($_POST['nombre'] ?? ''),
        'apellido' => normalizarTexto($_POST['apellido'] ?? ''),
        'correo'   => strtolower(trim($_POST['correo'] ?? '')),
        'usuario'  => strtolower(trim($_POST['usuario'] ?? '')),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'clave'    => $_POST['clave'] ?? '',
    ];

    if (!$rolModel->existe($datos['id_rol'])) {
        $errores[] = 'El rol seleccionado no es válido.';
    }
    foreach ([['nombre', 'nombre'], ['apellido', 'apellido']] as [$campo, $etiqueta]) {
        if (($error = validarNombre($datos[$campo], $etiqueta)) !== null) {
            $errores[] = $error;
        }
    }
    if (($error = validarLongitud($datos['usuario'], 4, 50, 'nombre de usuario')) !== null) {
        $errores[] = $error;
    } elseif (!preg_match('/^[a-z0-9._-]+$/', $datos['usuario'])) {
        $errores[] = 'El nombre de usuario solo puede contener letras minúsculas, números, puntos, guiones y guiones bajos.';
    } elseif ($usuarioModel->existeUsuario($datos['usuario'])) {
        $errores[] = 'El nombre de usuario ya está en uso.';
    }
    if (($error = validarLongitud($datos['correo'], 1, 150, 'correo')) !== null) {
        $errores[] = $error;
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no tiene un formato válido.";
    } elseif ($usuarioModel->existeCorreo($datos['correo'])) {
        $errores[] = 'El correo ya está registrado.';
    }
    if (strlen($datos['clave']) < 8 || !preg_match('/[A-Za-z]/', $datos['clave']) || !preg_match('/\d/', $datos['clave'])) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres, una letra y un número.';
    }
    if (($error = validarTelefono($datos['telefono'])) !== null) {
        $errores[] = $error;
    }

    if (empty($errores)) {
        try {
            $usuarioModel->crear($datos);
            header("Location: usuarios_listar.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            if ($e->getCode() === '23000' && $usuarioModel->existeCorreo($datos['correo'])) {
                $errores[] = 'El correo ya está registrado.';
            } elseif ($e->getCode() === '23000' && $usuarioModel->existeUsuario($datos['usuario'])) {
                $errores[] = 'El nombre de usuario ya está en uso.';
            } else {
                $errores[] = 'No se pudo registrar el usuario.';
            }
        }
    }
}

$roles = $rolModel->obtenerTodos();
require_once __DIR__ . '/../views/usuarios/crear.php';