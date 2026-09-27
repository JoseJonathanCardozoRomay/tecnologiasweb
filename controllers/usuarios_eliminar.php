<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios_listar.php');
    exit;
}

csrf_validar();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    header('Location: usuarios_listar.php');
    exit;
}

try {
    (new UsuarioModel($pdo))->eliminar($id);
    flash_set('success', 'Usuario eliminado correctamente.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo eliminar: el usuario tiene un perfil de estudiante o tutor asociado.');
}

header('Location: usuarios_listar.php');
exit;