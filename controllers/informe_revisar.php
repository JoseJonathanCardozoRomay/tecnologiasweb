<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','tutor'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/InformeAvanceModel.php';
$modelo = new InformeAvanceModel();
$id = (int)($_GET['id'] ?? 0);
$informe = $modelo->obtenerPorId($id);
if (!$informe) die('No encontrado');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = $_POST['estado'] ?? 'observado';
    $obs = trim($_POST['observaciones_revision'] ?? '');
    $modelo->revisar($id, $_SESSION['id_usuario'], $estado, $obs);
    header('Location: informes_listar.php');
    exit;
}

require_once __DIR__ . '/../views/seguimiento/informe_revisar.php';