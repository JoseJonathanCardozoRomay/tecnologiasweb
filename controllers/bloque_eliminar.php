<?php
/**
 * Eliminar Bloque Horario
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=bloques_listar');
    exit;
}

global $conexion;

$stmt = $conexion->prepare("DELETE FROM bloques_horarios WHERE id_bloque = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: index.php?accion=bloques_listar');
exit;