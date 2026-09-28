<?php
/**
 * Eliminar Usuario — BORRA EN ORDEN para evitar restricciones
 * Orden: Tutorias → Estudiante/Tutor → Usuario
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=usuarios_listar');
    exit;
}

// ✅ No permitir eliminarse a sí mismo
if ($id === (int)($_SESSION['id_usuario'] ?? 0)) {
    echo "<script>alert('⚠️ No puedes eliminar tu propio usuario');history.back();</script>";
    exit;
}

global $conexion;

try {
    // ✅ PASO 1: Obtener el id_estudiante si existe
    $id_estudiante = null;
    $stmt = $conexion->prepare("SELECT id_estudiante FROM estudiantes WHERE id_usuario = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fila) {
        $id_estudiante = $fila['id_estudiante'];
    }

    // ✅ PASO 2: Obtener el id_tutor si existe
    $id_tutor = null;
    $stmt = $conexion->prepare("SELECT id_tutor FROM tutores WHERE id_usuario = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fila) {
        $id_tutor = $fila['id_tutor'];
    }

    // ✅ PASO 3: Borrar tutorías donde aparece como ESTUDIANTE
    if ($id_estudiante) {
        $stmt = $conexion->prepare("DELETE FROM tutorias WHERE id_estudiante = :id_estudiante");
        $stmt->bindParam(':id_estudiante', $id_estudiante);
        $stmt->execute();
    }

    // ✅ PASO 4: Borrar tutorías donde aparece como TUTOR
    if ($id_tutor) {
        $stmt = $conexion->prepare("DELETE FROM tutorias WHERE id_tutor = :id_tutor");
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->execute();
    }

    // ✅ PASO 5: Borrar registro de ESTUDIANTE
    if ($id_estudiante) {
        $stmt = $conexion->prepare("DELETE FROM estudiantes WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // ✅ PASO 6: Borrar registro de TUTOR
    if ($id_tutor) {
        $stmt = $conexion->prepare("DELETE FROM tutores WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // ✅ PASO 7: FINALMENTE borrar el USUARIO
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // ✅ ÉXITO
    header('Location: index.php?accion=usuarios_listar');
    exit;

} catch (PDOException $e) {
    echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "');history.back();</script>";
    exit;
}