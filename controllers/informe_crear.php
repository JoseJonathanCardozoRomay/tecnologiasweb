<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','estudiante'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/InformeAvanceModel.php';
$modelo = new InformeAvanceModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tutoria' => (int)($_POST['id_tutoria'] ?? 0),
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'progreso_porcentaje' => (int)($_POST['progreso_porcentaje'] ?? 0),
        'id_usuario_crea' => $_SESSION['id_usuario']
    ];
    if ($datos['id_tutoria'] <= 0 || empty($datos['titulo'])) {
        $error = 'Completa los campos obligatorios';
    } else {
        $modelo->crear($datos);
        header('Location: informes_listar.php');
        exit;
    }
}

require_once __DIR__ . '/../models/TutoriaModel.php';
$tutoriaModel = new TutoriaModel();
$tutorias = $tutoriaModel->listarTodos();
require_once __DIR__ . '/../views/seguimiento/informe_crear.php';