<?php
/**
 * Eliminar Materia — SOLO ADMINISTRADOR
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';

// === BLOQUEO DE PERMISO ===
if ($rol_actual !== 'administrador') {
    echo "<script>alert('No tienes permiso para eliminar materias'); window.location='index.php';</script>";
    exit;
}

$modelo = new MateriaModel();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $modelo->eliminar($id);
}

header('Location: index.php?accion=materias_listar');
exit;