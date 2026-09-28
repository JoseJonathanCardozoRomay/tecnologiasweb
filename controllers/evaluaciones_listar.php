<?php
/**
 * Listado de Evaluaciones — Coincide con modelo y vista
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'tutor', 'estudiante']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';

$modelo = new EvaluacionModel();
$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = (int)($_SESSION['id_usuario'] ?? 0);

$evaluaciones = [];
if ($rol_actual === 'administrador') {
    $evaluaciones = $modelo->listarTodas();
} elseif ($rol_actual === 'tutor') {
    $evaluaciones = $modelo->listarPorTutor($id_usuario_actual);
} elseif ($rol_actual === 'estudiante') {
    $evaluaciones = $modelo->listarPorEstudiante($id_usuario_actual);
}

require_once __DIR__ . '/../views/evaluaciones/listar.php';