<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$carreraModel = new CarreraModel($pdo);
$carreras = $carreraModel->obtenerTodas();

require_once __DIR__ . '/../views/carreras/listar.php';
