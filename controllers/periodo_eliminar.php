<?php
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);


require_once __DIR__ . '/../models/PeriodoTutoriaModel.php';

$modelo = new PeriodoTutoriaModel();
$modelo->eliminar($_GET['id']);

header('Location: index.php?accion=periodos_listar');
exit;
?>