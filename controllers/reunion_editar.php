<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','tutor'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/ReunionSeguimientoModel.php';
$modelo = new ReunionSeguimientoModel();
$id = (int)($_GET['id'] ?? 0);
$reunion = $modelo->obtenerPorId($id);
if (!$reunion) {
    die('Reunión no encontrada');
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'fecha_reunion' => $_POST['fecha_reunion'] ?? '',
        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
        'enlace' => trim($_POST['enlace'] ?? ''),
        'asistencia' => $_POST['asistencia'] ?? 'pendiente',
        'observaciones_inicio' => trim($_POST['observaciones_inicio'] ?? '')
    ];
    if (empty($datos['titulo']) || empty($datos['fecha_reunion'])) {
        $error = 'Completa los campos obligatorios';
    } else {
        $modelo->editar($id, $datos);
        header('Location: reuniones_listar.php');
        exit;
    }
}

require_once __DIR__ . '/../views/seguimiento/reunion_editar.php';