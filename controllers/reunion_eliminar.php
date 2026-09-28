<?php
/**
 * Eliminar Reunión de Seguimiento
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor']);

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    require_once __DIR__ . '/../config/conexion.php';
    global $conexion;
    $stmt = $conexion->prepare("DELETE FROM reuniones_seguimiento WHERE id_reunion = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

header('Location: index.php?accion=reuniones_listar');
exit;