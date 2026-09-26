<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','estudiante'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/InformeAvanceModel.php';
$modelo = new InformeAvanceModel();
$id = (int)($_GET['id'] ?? 0);
$informe = $modelo->obtenerPorId($id);
if (!$informe) {
    die('Informe no encontrado');
}
if ($informe['estado'] !== 'borrador' && $_SESSION['rol_nombre'] !== 'administrador') {
    echo "<script>alert('Solo se pueden editar informes en borrador');history.back();</script>";
    exit;
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'progreso_porcentaje' => (int)($_POST['progreso_porcentaje'] ?? 0),
        'estado' => 'borrador'
    ];
    if (empty($datos['titulo'])) {
        $error = 'Completa los campos obligatorios';
    } else {
        $modelo->editar($id, $datos);
        header('Location: informes_listar.php');
        exit;
    }
}

require_once __DIR__ . '/../views/seguimiento/informe_editar.php';