<?php
/**
 * Eliminar Periodo
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=periodos_listar');
    exit;
}

global $conexion;

// ✅ Verificar que no tenga datos dependientes antes de borrar
$tiene_datos = false;
// Aquí puedes agregar comprobaciones si hay tablas que dependan de periodos

if (!$tiene_datos) {
    $stmt = $conexion->prepare("DELETE FROM periodos_tutoria WHERE id_periodo = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

header('Location: index.php?accion=periodos_listar');
exit;