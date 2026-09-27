<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgCalendarioModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();

$cohorteId = (int) ($_GET['cohorte'] ?? 0);
if ($cohorteId <= 0) {
    $abiertas = $cohorteModel->obtenerTodasAbiertas();
    $cohorteId = !empty($abiertas)
        ? (int) $abiertas[0]['id_cohorte_mg']
        : (!empty($cohortes) ? (int) $cohortes[0]['id_cohorte_mg'] : 0);
}

$cohorteActual = $cohorteId > 0 ? $cohorteModel->obtenerPorId($cohorteId) : null;

$calendarioModel = new MgCalendarioModel($pdo);
$hitos = $cohorteId > 0 ? $calendarioModel->listarPorCohorte($cohorteId) : [];
$totalHitos = count($hitos);

require_once __DIR__ . '/../views/mg/calendario/listar.php';