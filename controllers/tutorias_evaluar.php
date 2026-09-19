<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $idTutoria = $_POST['id_tutoria'] ?? null;
    $calificacion = (int)($_POST['calificacion'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');

    $tutoriaModel = new TutoriaModel($pdo);
    $tutoria = $tutoriaModel->obtenerPorId($idTutoria);
    if ($idTutoria && $tutoria && $tutoria['estado'] === 'realizada' && (int) ($tutoria['id_usuario'] ?? 0) === (int) $_SESSION['id_usuario'] && $calificacion >= 1 && $calificacion <= 5) {
        $evaluacionModel = new EvaluacionModel($pdo);
        try {
            $evaluacionModel->registrar($idTutoria, $calificacion, $comentario);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? '../views/estudiante/panel.php';
header("Location: " . $referer);
exit;
