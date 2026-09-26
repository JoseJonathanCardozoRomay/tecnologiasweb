<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/RegistroAccesoModel.php';
require_once __DIR__ . '/../includes/sesion.php';

$modeloUsuario = new UsuarioModel($pdo);
$modeloAcceso = new RegistroAccesoModel($pdo);

// Si el usuario ya ingresó, lo enviamos a la página principal
if (usuarioAutenticado()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$usuarioIngresado = '';

// Procesamos los datos solamente cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioIngresado = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $ipOrigen = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

    if ($usuarioIngresado === '' || $contrasena === '') {
        $error = 'Completa el usuario y la contraseña.';
    } else {
        $usuarioEncontrado = $modeloUsuario->buscarPorUsuario(
            $usuarioIngresado
        );

        $idUsuario = $usuarioEncontrado['id_usuario'] ?? null;

        if (!$usuarioEncontrado) {
            $modeloAcceso->registrar(
                null,
                'fallido',
                $ipOrigen
            );

            $error = 'El usuario o la contraseña son incorrectos.';
        } elseif ($usuarioEncontrado['estado'] !== 'activo') {
            $modeloAcceso->registrar(
                $idUsuario,
                'fallido',
                $ipOrigen
            );

            $error = 'La cuenta se encuentra inactiva.';
        } elseif (!password_verify(
            $contrasena,
            $usuarioEncontrado['contrasena_hash']
        )) {
            $modeloAcceso->registrar(
                $idUsuario,
                'fallido',
                $ipOrigen
            );

            $error = 'El usuario o la contraseña son incorrectos.';
        } else {
            // Renovamos el identificador para proteger la sesión iniciada
            session_regenerate_id(true);

            $_SESSION['usuario'] = [
                'id_usuario' => $usuarioEncontrado['id_usuario'],
                'nombre' => $usuarioEncontrado['nombre'],
                'apellido' => $usuarioEncontrado['apellido'],
                'usuario' => $usuarioEncontrado['usuario'],
                'rol' => $usuarioEncontrado['nombre_rol']
            ];

            $modeloAcceso->registrar(
                $idUsuario,
                'exitoso',
                $ipOrigen
            );

            header('Location: ../index.php');
            exit;
        }
    }
}

// Estos datos serán utilizados por la vista del formulario
$tituloPagina = 'Iniciar sesión';
$rutaBase = '../';

require_once __DIR__ . '/../views/auth/login.php';