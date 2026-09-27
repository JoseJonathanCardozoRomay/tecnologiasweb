<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgAlertaModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_alertas.php');
    exit;
}

csrf_validar();

$alertaModel = new MgAlertaModel($pdo);
$codigo = limpiarTexto($_POST['codigo'] ?? '', 10);
$titulo = limpiarTexto($_POST['titulo'] ?? '', 150);
$mensaje = limpiarTexto($_POST['mensaje'] ?? '', 300);
$referencia = (int) ($_POST['id_referencia'] ?? 0);
$nota = trim($_POST['nota'] ?? '');

if ($codigo === '' || $titulo === '' || $mensaje === '') {
    flash_set('error', 'Faltan datos para registrar la atención de la alerta.');
} elseif ($nota === '') {
    flash_set('error', 'Debes indicar una nota para marcar la alerta como atendida.');
} else {
    try {
        $alertaModel->atender($codigo, $titulo, $mensaje, $referencia ?: null,
            (int) $_SESSION['id_usuario'], mb_substr($nota, 0, 255));
        flash_set('success', 'Alerta ' . $codigo . ' registrada como atendida.');
    } catch (PDOException $e) {
        flash_set('error', 'No se pudo registrar la atención de la alerta.');
    }
}

header('Location: mg_alertas.php');
exit;