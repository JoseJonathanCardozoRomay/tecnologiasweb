<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_parametros_listar.php');
    exit;
}

csrf_validar();

$parametroModel = new MgParametroModel($pdo);
$valores = $_POST['valor'] ?? [];

if (!is_array($valores)) {
    flash_set('error', 'Los datos enviados no son válidos.');
    header('Location: mg_parametros_listar.php');
    exit;
}

$actualizados = 0;
foreach ($parametroModel->obtenerTodas() as $parametro) {
    $clave = $parametro['clave'];
    if (!array_key_exists($clave, $valores)) {
        continue;
    }
    $parametroModel->actualizar($clave, (string) $valores[$clave]);
    $actualizados++;
}

flash_set('success', "Parámetros actualizados correctamente ($actualizados).");
header('Location: mg_parametros_listar.php');
exit;