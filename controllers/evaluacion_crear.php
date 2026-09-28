<?php
/**
 * Crear Evaluación — ✅ Coincide EXACTAMENTE con tu tabla
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';

$modelo = new EvaluacionModel();
$tutoriaModel = new TutoriaModel();
$error = '';

if ($_SESSION['rol_nombre'] === 'estudiante') {
    $tutorias = $tutoriaModel->listarPorEstudiante($_SESSION['id_usuario']);
} else {
    $tutorias = $tutoriaModel->listarTodos();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $id_tutoria = (int)($_POST['id_tutoria'] ?? 0);
        $calificacion = trim($_POST['calificacion'] ?? '');
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id_tutoria <= 0) {
            $error = 'Selecciona una tutoría';
        } elseif (empty($calificacion)) {
            $error = 'La calificación es obligatoria';
        } elseif (!ctype_digit($calificacion)) {
            $error = 'La calificación debe ser un número entero (ej: 8, 9, 10)';
        } elseif ($calificacion < 1 || $calificacion > 10) {
            $error = 'La calificación debe estar entre 1 y 10';
        } else {
            $datos = [
                'id_tutoria' => $id_tutoria,
                'calificacion' => $calificacion,
                'comentario' => $comentario,
                'fecha_evaluacion' => date('Y-m-d H:i:s')
            ];

            if ($modelo->crear($datos)) {
                header('Location: index.php?accion=evaluaciones_listar');
                exit;
            } else {
                $error = 'Error al guardar la evaluación';
            }
        }
    }
}

require_once __DIR__ . '/../views/evaluaciones/crear.php';