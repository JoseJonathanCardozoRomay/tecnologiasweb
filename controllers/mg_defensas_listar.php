<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();
$cohorteDefault = 0;
foreach ($cohortes as $c) {
    if ($c['estado'] === 'abierta') {
        $cohorteDefault = (int) $c['id_cohorte_mg'];
        break;
    }
}
if ($cohorteDefault === 0 && $cohortes) {
    $cohorteDefault = (int) $cohortes[0]['id_cohorte_mg'];
}

$cohorteId = (int) ($_GET['cohorte'] ?? $cohorteDefault);
$estado = limpiarTexto($_GET['estado'] ?? '', 20);
$defensaModel = new MgDefensaModel($pdo);

$defensas = $cohorteId > 0 ? $defensaModel->listarPorCohorte($cohorteId, $estado) : [];
$cohorteActual = $cohorteId > 0 ? $cohorteModel->obtenerPorId($cohorteId) : null;

require_once __DIR__ . '/../views/mg/defensas/listar.php';