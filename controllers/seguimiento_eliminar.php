<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

$modelo = new SeguimientoSesionModel();
$modelo->eliminar($_GET['id']);

header('Location: index.php?accion=seguimientos_listar');
exit;