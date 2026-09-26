<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','estudiante'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/InformeAvanceModel.php';
$modelo = new InformeAvanceModel();
$id = (int)($_GET['id'] ?? 0);
$modelo->enviar($id, $_SESSION['id_usuario']);
header('Location: informes_listar.php');
exit;