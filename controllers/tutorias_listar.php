<?php
/**
 * Listar Tutorías — Con permisos por rol compatibles con tu sesión
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$modelo = new TutoriaModel();

// ✅ Leer rol con los nombres que usa tu sesión
$rol_actual = $_SESSION['rol_nombre'] ?? $_SESSION['usuario']['nombre_rol'] ?? '';
$id_usuario_actual = (int)($_SESSION['id_usuario'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);

// ✅ Filtrar según el rol
if ($rol_actual === 'administrador') {
    // Admin ve TODAS las tutorías
    $tutorias = $modelo->listarTodas();
} elseif ($rol_actual === 'estudiante') {
    // Estudiante ve solo las suyas
    $tutorias = $modelo->listarPorEstudiante($id_usuario_actual);
} elseif ($rol_actual === 'tutor') {
    // Tutor ve solo las que le asignaron
    $tutorias = $modelo->listarPorTutor($id_usuario_actual);
} else {
    $tutorias = [];
}

require_once __DIR__ . '/../views/tutorias/listar.php';