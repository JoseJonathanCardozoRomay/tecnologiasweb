<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_defensas_listar.php');
    exit;
}

csrf_validar();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    header('Location: mg_defensas_listar.php');
    exit;
}

$model = new MgDefensaModel($pdo);
$defensa = $model->obtenerPorId($id);
$cohorteId = $defensa ? (int) $defensa['id_cohorte_mg'] : 0;

try {
    $model->eliminar($id);
    flash_set('success', 'Defensa eliminada correctamente.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo eliminar la defensa.');
}

header('Location: mg_defensas_listar.php?cohorte=' . $cohorteId);
exit;