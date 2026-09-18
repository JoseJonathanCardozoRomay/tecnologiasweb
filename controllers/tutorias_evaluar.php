<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTutoria = $_POST['id_tutoria'] ?? null;
    $calificacion = (int)($_POST['calificacion'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');

    if ($idTutoria && $calificacion >= 1 && $calificacion <= 5) {
        $evaluacionModel = new EvaluacionModel($pdo);
        try {
            $evaluacionModel->registrar($idTutoria, $calificacion, $comentario);
        } catch (PDOException $e) {
            // Manejo de excepción
        }
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? '../views/estudiante/panel.php';
header("Location: " . $referer);
exit;
