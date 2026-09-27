<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgAlertaModel.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$parametroModel = new MgParametroModel($pdo);
$parametros = [];
foreach ($parametroModel->obtenerTodas() as $p) {
    $parametros[$p['clave']] = $p['valor'];
}

$alertaModel = new MgAlertaModel($pdo);
$alertas = $alertaModel->calcular($parametros);
$historial = $alertaModel->historial(60);

require_once __DIR__ . '/../views/mg/alertas/panel.php';