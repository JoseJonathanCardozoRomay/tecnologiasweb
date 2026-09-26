<?php
/**
 * Eliminar Tribunal / Jurado
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgTribunalModel.php';

$modelo = new MgTribunalModel();
$id_tribunal = (int)($_GET['id'] ?? 0);

if ($id_tribunal > 0) {
    $modelo->eliminar($id_tribunal);
}

header('Location: index.php?accion=mg_tribunales_listar');
exit;