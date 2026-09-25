<?php
/**
 * Listar Estudiantes — Permisos por Rol
 * Admin: ve TODOS | Tutor: ve lista | Estudiante: solo se ve a sí mismo
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new EstudianteModel();
$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

if ($rol_actual === 'administrador') {
    // ✅ Admin ve TODOS
    $estudiantes = $modelo->listarTodos();
} elseif ($rol_actual === 'tutor') {
    // ✅ Tutor ve lista completa (solo lectura)
    $estudiantes = $modelo->listarTodos();
} elseif ($rol_actual === 'estudiante' && $id_usuario_actual > 0) {
    // ✅ Estudiante SOLO se ve a sí mismo
    $estudiantes = $modelo->listarPorUsuario($id_usuario_actual);
} else {
    $estudiantes = [];
}

require_once __DIR__ . '/../views/estudiantes/listar.php';