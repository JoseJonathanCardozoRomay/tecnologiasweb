<?php
/**
 * Eliminar Disponibilidad Horaria
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=disponibilidad_listar');
    exit;
}

global $conexion;
$rol_actual = $_SESSION['rol_nombre'] ?? '';

// Verificar pertenencia: tutor solo elimina la suya
if ($rol_actual === 'tutor') {
    $stmt = $conexion->prepare("
        SELECT t.id_usuario
        FROM disponibilidad_tutor d
        INNER JOIN tutores t ON d.id_tutor = t.id_tutor
        WHERE d.id_disponibilidad = :id
    ");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro || $registro['id_usuario'] !== $_SESSION['id_usuario']) {
        echo "<script>alert('No tienes permiso para eliminar esta disponibilidad');history.back();</script>";
        exit;
    }
}

// Eliminar
$stmt = $conexion->prepare("DELETE FROM disponibilidad_tutor WHERE id_disponibilidad = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: index.php?accion=disponibilidad_listar');
exit;