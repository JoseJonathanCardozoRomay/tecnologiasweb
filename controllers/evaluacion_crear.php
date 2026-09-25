<?php
/**
 * Crear Evaluación — Solo Estudiante
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';

$rol = $_SESSION['usuario']['nombre_rol'] ?? '';

// ✅ SOLO Estudiante puede evaluar
if ($rol !== 'estudiante') {
    echo "<script>alert('Solo los estudiantes pueden evaluar tutorías');history.back();</script>";
    exit;
}

$id_tutoria = (int)($_GET['id_tutoria'] ?? 0);
if ($id_tutoria <= 0) {
    echo "<script>alert('Tutoría no especificada');history.back();</script>";
    exit;
}

$modelo = new EvaluacionModel();
$error = '';

if ($modelo->yaExiste($id_tutoria)) {
    header("Location: index.php?accion=tutorias_listar");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calificacion = (int)($_POST['calificacion'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');
    
    if ($calificacion < 1 || $calificacion > 5) {
        $error = 'La calificación debe ser entre 1 y 5';
    } else {
        $modelo->crear($id_tutoria, $calificacion, $comentario);
        header("Location: index.php?accion=tutorias_listar");
        exit;
    }
}

require_once __DIR__ . '/../views/evaluaciones/crear.php';