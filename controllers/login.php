<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($usuario) || empty($contrasena)) {
        $error = 'Escribe usuario y contraseña';
    } else {
        global $conexion;
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 'activo'");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos_usuario && password_verify($contrasena, $datos_usuario['contrasena_hash'])) {
            iniciarSesion($datos_usuario);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}

require_once __DIR__ . '/../views/login.php';