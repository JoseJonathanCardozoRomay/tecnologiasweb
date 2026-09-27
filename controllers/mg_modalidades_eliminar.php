<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_modalidades_listar.php');
    exit;
}

csrf_validar();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    header('Location: mg_modalidades_listar.php');
    exit;
}

try {
    (new MgModalidadModel($pdo))->eliminar($id);
    flash_set('success', 'Modalidad eliminada correctamente.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo eliminar la modalidad: tiene expedientes asociados.');
}

header('Location: mg_modalidades_listar.php');
exit;