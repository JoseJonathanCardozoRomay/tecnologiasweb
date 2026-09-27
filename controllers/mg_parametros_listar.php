<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgParametroModel.php';

$parametroModel = new MgParametroModel($pdo);
$parametros = $parametroModel->obtenerTodas();

require_once __DIR__ . '/../views/mg/parametros.php';