<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgReporteCohorteModel.php';

$modelo = new MgReporteCohorteModel();
$id = (int)($_GET['id'] ?? 0);
$reporte = $modelo->obtenerPorId($id);

if (!$reporte) {
    header('Location: index.php?accion=mg_reporte_cohorte_listar');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_cohorte' => trim($_POST['nombre_cohorte'] ?? ''),
        'anio_inicio' => (int)($_POST['anio_inicio'] ?? 0),
        'anio_fin' => (int)($_POST['anio_fin'] ?? 0),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'total_estudiantes' => (int)($_POST['total_estudiantes'] ?? 0),
        'estado' => trim($_POST['estado'] ?? 'activa')
    ];

    if (empty($datos['nombre_cohorte']) || $datos['anio_inicio'] <= 0) {
        $error = 'Completa los campos obligatorios';
    } else {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=mg_reporte_cohorte_listar');
            exit;
        }
        $error = 'Error al guardar los cambios';
    }
}

require_once __DIR__ . '/../views/mg_reporte_cohorte_editar.php';