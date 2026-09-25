<?php
/**
 * Listar Evaluaciones — Permisos por rol
 * Estudiante: solo las suyas | Tutor: las que le dejaron | Admin: TODAS
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';

$modelo = new EvaluacionModel();
$usuario_actual = $_SESSION['usuario'] ?? [];
$rol_actual = $usuario_actual['nombre_rol'] ?? '';
$id_usuario_actual = (int)($usuario_actual['id_usuario'] ?? 0);

// ✅ Filtrar según rol
$evaluaciones = [];
if ($rol_actual === 'administrador') {
    $evaluaciones = $modelo->listarTodas();
} elseif ($rol_actual === 'tutor') {
    $evaluaciones = $modelo->listarPorTutor($id_usuario_actual);
} elseif ($rol_actual === 'estudiante') {
    $evaluaciones = $modelo->listarPorEstudiante($id_usuario_actual);
}

require_once __DIR__ . '/../views/evaluaciones/listar.php';