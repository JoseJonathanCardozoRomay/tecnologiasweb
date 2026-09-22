<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$seguimientos = $modelo->listar();

require_once __DIR__ . '/../views/seguimientos/listar.php';