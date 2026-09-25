<?php
/**
 * Listado de Seguimiento de Sesiones — Permisos por rol
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

// Filtrar según rol
if ($rol_actual === 'tutor') {
    $seguimientos = $modelo->listarPorTutor($id_usuario_actual);
} elseif ($rol_actual === 'estudiante') {
    $seguimientos = $modelo->listarPorEstudiante($id_usuario_actual);
} else {
    $seguimientos = $modelo->listarTodos();
}

// Ruta CORREGIDA: sube un nivel con ../ y entra a views
require_once __DIR__ . '/../views/seguimientos/listar.php';