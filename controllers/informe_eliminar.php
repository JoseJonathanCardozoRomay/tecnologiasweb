<?php
/**
 * Eliminar Informe de Avance
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=informes_listar');
    exit;
}

global $conexion;

// Verificar que existe
$stmt = $conexion->prepare("SELECT * FROM informes_avance WHERE id_informe = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$informe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$informe) {
    header('Location: index.php?accion=informes_listar');
    exit;
}

// Estudiante solo elimina los suyos
$rol_actual = $_SESSION['rol_nombre'] ?? '';
if ($rol_actual === 'estudiante' && (int)($informe['id_usuario_crea'] ?? 0) !== (int)($_SESSION['id_usuario'] ?? 0)) {
    echo "<script>alert('No tienes permiso para eliminar este informe'); window.location='index.php?accion=informes_listar';</script>";
    exit;
}

// Eliminar
$stmt = $conexion->prepare("DELETE FROM informes_avance WHERE id_informe = :id");
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header('Location: index.php?accion=informes_listar');
    exit;
} else {
    echo "<script>alert('Error al eliminar el informe'); window.history.back();</script>";
}