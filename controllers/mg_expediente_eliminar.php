<?php
/**
 * Controlador — Eliminar Expediente
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';

$modelo = new MgExpedienteModel();
$id = (int)($_GET['id_expediente'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('Expediente no válido');location.href='index.php?accion=mg_expedientes_listar';</script>";
    exit;
}

if ($modelo->eliminar($id)) {
    echo "<script>alert('Expediente eliminado correctamente');location.href='index.php?accion=mg_expedientes_listar';</script>";
} else {
    echo "<script>alert('No se pudo eliminar. Puede tener registros asociados.');history.back();</script>";
}
exit;