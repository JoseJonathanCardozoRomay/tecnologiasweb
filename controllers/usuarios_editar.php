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

$id = $_GET['id'] ?? $_POST['id_usuario'] ?? null;
if (!$id) {
    header('Location: usuarios_listar.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

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
            $antes = $usuarioModel->obtenerPorId($id);
            $usuarioModel->actualizar($id, $datos);
            $bitacora->registrar((int) $_SESSION['id_usuario'], (int) $id, 'editar', [
                'antes' => [
                    'rol'     => $antes['id_rol'] ?? null,
                    'nombre'  => ($antes['nombre'] ?? '') . ' ' . ($antes['apellido'] ?? ''),
                    'correo'  => $antes['correo'] ?? null,
                    'usuario' => $antes['usuario'] ?? null,
                    'estado'  => $antes['estado'] ?? null,
                ],
                'despues' => [
                    'rol'     => $datos['id_rol'],
                    'nombre'  => $datos['nombre'] . ' ' . $datos['apellido'],
                    'correo'  => $datos['correo'],
                    'usuario' => $datos['usuario'],
                    'estado'  => $datos['estado'],
                ],
            ]);
            flash_set('success', 'Usuario actualizado correctamente.');
            header('Location: usuarios_listar.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "No se pudo actualizar: el correo o el usuario ya están en uso.";
        }
    }
}

$usuario_actual = $usuarioModel->obtenerPorId($id);
if (!$usuario_actual) {
    header('Location: usuarios_listar.php');
    exit;
}

$roles = $rolModel->obtenerTodos();
require_once __DIR__ . '/../views/usuarios/editar.php';