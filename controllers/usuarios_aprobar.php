<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/flash.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
csrf_validar();
$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $modelo = new UsuarioModel($pdo);
    $usuario = $modelo->obtenerPorId($id);
    if ($usuario && $usuario['estado'] === 'pendiente') { $modelo->actualizarEstado($id, 'activo'); flash_set('success', 'Cuenta aprobada correctamente.'); }
}
header('Location: usuarios_listar.php');
exit;