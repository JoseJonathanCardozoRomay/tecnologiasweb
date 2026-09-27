<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Defensa no válida.');
    header('Location: mg_defensas_listar.php');
    exit;
}

$defensaModel = new MgDefensaModel($pdo);
$defensa = $defensaModel->obtenerConDetalle($id);
if (!$defensa) {
    flash_set('error', 'La defensa solicitada no existe.');
    header('Location: mg_defensas_listar.php');
    exit;
}

$config = new ConfiguracionModel($pdo);
$datosPagina = mgDatosInstitucion($config);

if (empty($defensa['correlativo'])) {
    $gestion = is_array($datosPagina) && ($datosPagina['gestion'] ?? '') !== '' ? $datosPagina['gestion'] : date('Y');
    $correlativo = mgSiguienteCorrelativo($pdo, 'citacion_defensa', $gestion);
    $defensaModel->asignarCorrelativo($id, $correlativo);
    $defensa['correlativo'] = $correlativo;
}

require_once __DIR__ . '/../views/mg/cartas/citacion_defensa.php';