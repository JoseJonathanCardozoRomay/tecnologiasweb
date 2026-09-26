<?php
/**
 * Editar Cohorte
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';

$modelo = new MgCohorteModel();
$id = (int)($_GET['id'] ?? 0);
$cohorte = $modelo->obtenerPorId($id);

if (!$cohorte) {
    header('Location: index.php?accion=mg_cohortes');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'nombre' => trim($_POST['nombre'] ?? ''),
        'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
        'fecha_fin' => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null,
        'activa' => isset($_POST['activa']) ? 1 : 0
    ];
    
    if (empty($datos['codigo']) || empty($datos['nombre']) || empty($datos['fecha_inicio'])) {
        $error = 'Completa los campos obligatorios';
    } else {
        $modelo->editar($id, $datos);
        header('Location: index.php?accion=mg_cohortes');
        exit;
    }
}

require_once __DIR__ . '/../views/mg_cohortes/editar.php';