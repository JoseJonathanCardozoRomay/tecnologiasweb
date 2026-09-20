<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';

$desde = trim((string)($_GET['desde'] ?? ''));
$hasta = trim((string)($_GET['hasta'] ?? ''));
$errores = [];

if ($desde !== '' && !fechaValida($desde)) {
    $errores[] = 'La fecha inicial no es válida.';
    $desde = '';
}
if ($hasta !== '' && !fechaValida($hasta)) {
    $errores[] = 'La fecha final no es válida.';
    $hasta = '';
}
if ($desde !== '' && $hasta !== '' && $desde > $hasta) {
    $errores[] = 'La fecha inicial no puede ser posterior a la fecha final.';
}

$sql = "SELECT ra.*, COALESCE(CONCAT(u.nombre,' ',u.apellido),'Usuario no disponible') AS usuario
        FROM registro_accesos ra
        LEFT JOIN usuarios u ON u.id_usuario=ra.id_usuario
        WHERE 1=1";
$params = [];

if ($desde !== '') {
    $sql .= ' AND ra.fecha_hora >= :desde';
    $params[':desde'] = $desde . ' 00:00:00';
}
if ($hasta !== '') {
    $sql .= ' AND ra.fecha_hora <= :hasta';
    $params[':hasta'] = $hasta . ' 23:59:59';
}

$sql .= ' ORDER BY ra.fecha_hora DESC LIMIT 1000';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();

$tituloPagina = 'Auditoría - Sistema de Tutorías';
require __DIR__.'/../views/auditoria/listar.php';
