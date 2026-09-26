<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgReporteCohorteModel.php';

$modelo = new MgReporteCohorteModel();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $modelo->eliminar($id);
}

header('Location: index.php?accion=mg_reporte_cohorte_listar');
exit;