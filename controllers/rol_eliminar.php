<?php
/**
 * Eliminar Rol
 */
require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $modelo->eliminar($id);
}

header('Location: index.php?accion=listar');
exit;