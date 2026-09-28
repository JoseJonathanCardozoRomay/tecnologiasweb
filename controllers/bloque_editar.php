<?php
/**
 * Editar Bloque Horario — CORREGIDO nombres de columnas
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

// ✅ Usa los nombres REALES de tus columnas
$stmt = $conexion->prepare("SELECT * FROM bloques_horarios WHERE id_bloque = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$bloque = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bloque) {
    header('Location: index.php?accion=bloques_listar');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $nombre_bloque = trim($_POST['nombre_bloque'] ?? '');
        $hora_inicio = trim($_POST['hora_inicio'] ?? '');
        $hora_fin = trim($_POST['hora_fin'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre_bloque)) {
            $error = 'El nombre es obligatorio';
        } elseif (empty($hora_inicio)) {
            $error = 'La hora de inicio es obligatoria';
        } elseif (empty($hora_fin)) {
            $error = 'La hora de fin es obligatoria';
        } elseif ($hora_inicio >= $hora_fin) {
            $error = 'La hora de fin debe ser posterior a la de inicio';
        } else {
            // ✅ ACTUALIZA con los nombres REALES de tu tabla
            $stmt = $conexion->prepare("
                UPDATE bloques_horarios 
                SET nombre_bloque = :nombre_bloque, 
                    hora_inicio = :hora_inicio, 
                    hora_fin = :hora_fin, 
                    descripcion = :descripcion
                WHERE id_bloque = :id
            ");
            $stmt->bindParam(':nombre_bloque', $nombre_bloque);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header('Location: index.php?accion=bloques_listar');
                exit;
            } else {
                $error = 'Error al actualizar el bloque';
            }
        }
    }
}

require_once __DIR__ . '/../views/bloques/editar.php';