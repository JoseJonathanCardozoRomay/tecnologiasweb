<?php
require_once __DIR__ . '/../models/AlertasSeguimientoModel.php';

$modelo = new AlertasSeguimientoModel();
$id_alerta = (int)($_GET['id'] ?? 0);
$id_usuario = $_SESSION['id_usuario'] ?? 0;

if ($id_alerta > 0 && $id_usuario > 0) {
    $modelo->marcarLeida($id_alerta, $id_usuario);
}

header('Location: index.php?accion=alertas_listar');
exit;