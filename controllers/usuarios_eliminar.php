<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

$usuarioModel = new UsuarioModel($pdo);

$id = $_GET['id'] ?? null;
if ($id) {
    try {
        $usuarioModel->eliminar($id);
    } catch (PDOException $e) {
        die("No se pudo eliminar: este usuario tiene un perfil de estudiante o tutor asociado. Elimina primero ese registro.");
    }
}

header("Location: usuarios_listar.php");
exit;