<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';

$desde = trim((string)($_GET['desde'] ?? ''));
$hasta = trim((string)($_GET['hasta'] ?? ''));

if ($desde !== '' && !fechaValida($desde)) {
    http_response_code(400);
    exit('La fecha inicial no es válida.');
}
if ($hasta !== '' && !fechaValida($hasta)) {
    http_response_code(400);
    exit('La fecha final no es válida.');
}
if ($desde !== '' && $hasta !== '' && $desde > $hasta) {
    http_response_code(400);
    exit('La fecha inicial no puede ser posterior a la fecha final.');
}

$sql = "SELECT ra.fecha_hora,
               COALESCE(CONCAT(u.nombre,' ',u.apellido),'Usuario no disponible') AS usuario,
               COALESCE(ra.accion,'ACCESO') AS accion,
               COALESCE(ra.modulo,'Autenticación') AS modulo,
               ra.resultado,
               COALESCE(ra.descripcion,'') AS descripcion,
               COALESCE(ra.ip_origen,'') AS ip_origen
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

$sql .= ' ORDER BY ra.fecha_hora DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$nombreArchivo = 'auditoria_' . ($desde !== '' ? $desde : 'todos') . '_' . ($hasta !== '' ? $hasta : 'todos') . '.txt';
$nombreArchivo = preg_replace('/[^A-Za-z0-9_.-]/', '_', $nombreArchivo) ?: 'auditoria.txt';

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('X-Content-Type-Options: nosniff');

// BOM para que Windows/Bloc de notas reconozca correctamente UTF-8.
echo "\xEF\xBB\xBF";
echo "SISTEMA DE TUTORÍAS UPDS - REPORTE DE AUDITORÍA\n";
echo str_repeat('=', 110) . "\n";
echo 'Filtro desde: ' . ($desde !== '' ? $desde : 'TODAS') . "\n";
echo 'Filtro hasta: ' . ($hasta !== '' ? $hasta : 'TODAS') . "\n";
echo 'Generado: ' . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 110) . "\n\n";
echo "FECHA/HORA | USUARIO | ACCIÓN | MÓDULO | RESULTADO | DESCRIPCIÓN | IP\n";
echo str_repeat('-', 110) . "\n";

while ($r = $stmt->fetch()) {
    $linea = [
        $r['fecha_hora'],
        $r['usuario'],
        $r['accion'],
        $r['modulo'],
        $r['resultado'],
        $r['descripcion'],
        $r['ip_origen'],
    ];
    $linea = array_map(static fn($v) => str_replace(["\r", "\n", '|'], [' ', ' ', '/'], (string)$v), $linea);
    echo implode(' | ', $linea) . "\n";
}

echo "\n" . str_repeat('=', 110) . "\n";
echo "FIN DEL REPORTE\n";
exit;
