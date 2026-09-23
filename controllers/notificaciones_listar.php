<?php
/**
 * Controlador — Listado de Notificaciones
 * Con botón para marcar como leídas
 */
require_once __DIR__ . '/../config/conexion.php';

// Si presiona "Marcar como leídas"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_leidas']) && isset($_SESSION['id_usuario'])) {
    $stmt = $conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
    $stmt->execute();
    header("Location: index.php?accion=notificaciones_listar");
    exit;
}

// Obtener notificaciones del usuario
if (isset($_SESSION['id_usuario'])) {
    $stmt = $conexion->prepare("SELECT * FROM notificaciones WHERE id_usuario = :id_usuario ORDER BY fecha_creacion DESC");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
    $stmt->execute();
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $notificaciones = [];
}

require_once __DIR__ . '/../views/notificaciones/listar.php';