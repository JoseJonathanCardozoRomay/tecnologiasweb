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
if ($informe['estado'] !== 'borrador' && $_SESSION['rol_nombre'] !== 'administrador') {
    echo "<script>alert('Solo se pueden eliminar informes en borrador');history.back();</script>";
    exit;
}
$modelo->eliminar($id);
header('Location: informes_listar.php');
exit;