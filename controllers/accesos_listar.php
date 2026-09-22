<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$accesos = $modelo->listarTodos();

require_once __DIR__ . '/../views/accesos/listar.php';