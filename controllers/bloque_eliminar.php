<?php
require_once __DIR__ . '/../models/BloqueHorarioModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?accion=bloques_listar');
    exit;
}

$modelo = new BloqueHorarioModel();
$modelo->eliminar($_GET['id']);

header('Location: index.php?accion=bloques_listar');
exit;