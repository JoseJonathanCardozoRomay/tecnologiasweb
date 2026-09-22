 <?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$seguimientos = $modelo->listarTodos();

require_once __DIR__ . '/../views/seguimiento/listar.php';