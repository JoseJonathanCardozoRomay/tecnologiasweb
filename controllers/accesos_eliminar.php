<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $modelo->eliminar($id);
}

header('Location: index.php?accion=accesos_listar');
exit;