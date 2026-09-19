<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /views/estudiante/panel.php');
    exit;
}

csrf_validar();
$idTutoria = filter_var($_POST['id_tutoria'] ?? null, FILTER_VALIDATE_INT);
$calificacion = (int) ($_POST['calificacion'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if (!$idTutoria || $calificacion < 1 || $calificacion > 5) {
    flash_set('danger', 'Los datos de la calificación no son válidos.');
} else {
    $tutoria = (new TutoriaModel($pdo))->obtenerPorId($idTutoria);
    if (!$tutoria || $tutoria['estado'] !== 'realizada' || (int) $tutoria['estudiante_id_usuario'] !== (int) $_SESSION['id_usuario']) {
        flash_set('danger', 'No puedes calificar esta tutoría.');
    } else {
        try {
            (new EvaluacionModel($pdo))->registrar($idTutoria, $calificacion, $comentario);
            flash_set('success', 'Calificación registrada.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            flash_set('danger', 'No se pudo registrar la calificación.');
        }
    }
}

header('Location: /views/estudiante/panel.php');
exit;
