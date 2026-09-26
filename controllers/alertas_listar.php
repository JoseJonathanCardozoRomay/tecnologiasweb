<?php
/**
 * Listado de Alertas de Seguimiento
 */
require_once __DIR__ . '/../models/AlertasSeguimientoModel.php';

$modelo = new AlertasSeguimientoModel();
$id_usuario = $_SESSION['id_usuario'] ?? 0;

$alertas = $modelo->listarPorUsuario($id_usuario);
$total_sin_leer = $modelo->contarSinLeer($id_usuario);

require_once __DIR__ . '/../views/alertas/listar.php';