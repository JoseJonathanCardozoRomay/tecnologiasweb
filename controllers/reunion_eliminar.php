<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','tutor'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/ReunionSeguimientoModel.php';
$modelo = new ReunionSeguimientoModel();
$id = (int)($_GET['id'] ?? 0);
$modelo->eliminar($id);
header('Location: reuniones_listar.php');
exit;