<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: mg_expedientes_listar.php');
    exit;
}

$expedienteModel = new MgExpedienteModel($pdo);
$expediente = $expedienteModel->obtenerPorId($id);
if (!$expediente) {
    header('Location: mg_expedientes_listar.php');
    exit;
}

$historial = $expedienteModel->historial($id);
$asignacionActiva = $expedienteModel->asignacionActiva($id);
$tutoresAfines = $expedienteModel->tutoresAfines($id);

$parametroModel = new MgParametroModel($pdo);
$cargaRecomendada = $parametroModel->int('tutor_carga_recomendada', 8);
$cargaMaxima = $parametroModel->int('tutor_carga_maxima', 10);
$diasLimite = $parametroModel->int('dias_limite_confirmacion_tutor', 5);
$requiereActa = $parametroModel->booleano('requiere_acta_aceptacion_tutor', true);

require_once __DIR__ . '/../views/mg/expedientes/ficha.php';