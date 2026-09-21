<?php
session_start();
require_once '../config/conexion.php';
require_once '../config/Response.php';
require_once '../models/UsuarioModel.php';

$esJSON = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($esJSON) {
        Response::error('Método no permitido.', 405);
    }
    header('Location: ../views/login/login.php');
    exit;
}

$raw = file_get_contents('php://input');
$cuerpoJSON = json_decode($raw, true);

$usuarioInput = trim($_POST['usuario'] ?? $cuerpoJSON['usuario'] ?? '');
$contrasenaInput = $_POST['contrasena'] ?? $cuerpoJSON['contrasena'] ?? '';

$modelo = new UsuarioModel($pdo);
$usuario = $modelo->obtenerPorUsuario($usuarioInput);

function registrarAcceso(PDO $pdo, $id_usuario, $resultado): void
{
    $pdo->prepare("INSERT INTO registro_accesos (id_usuario, ip_origen, resultado) VALUES (?, ?, ?)")
        ->execute([$id_usuario, $_SERVER['REMOTE_ADDR'], $resultado]);
}

if ($usuario && $usuario['estado'] === 'activo' && password_verify($contrasenaInput, $usuario['contrasena_hash'])) {
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['nombre_rol'];

    registrarAcceso($pdo, $usuario['id_usuario'], 'exitoso');

    if ($esJSON) {
        Response::json([
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'rol'        => $usuario['nombre_rol'],
        ], 200, 'Inicio de sesión correcto.');
    }

    switch ($usuario['nombre_rol']) {
        case 'administrador':
            header('Location: ../controllers/usuarios_listar.php');
            break;
        case 'tutor':
            header('Location: ../views/tutor/panel.php');
            break;
        case 'estudiante':
            header('Location: ../views/estudiante/panel.php');
            break;
        default:
            header('Location: ../views/login/login.php');
    }
    exit;
}

if ($usuario) {
    registrarAcceso($pdo, $usuario['id_usuario'], 'fallido');
}

if ($esJSON) {
    Response::error('Usuario o contraseña incorrectos, o cuenta inactiva.', 401);
}

$_SESSION['login_error'] = 'Usuario o contraseña incorrectos, o cuenta inactiva.';
header('Location: ../views/login/login.php');
exit;