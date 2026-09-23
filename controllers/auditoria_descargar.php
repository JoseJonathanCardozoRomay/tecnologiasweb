<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
require_once __DIR__.'/../includes/excel_exportador.php';
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

$filas = [];
while ($r = $stmt->fetch()) {
    // Normalizamos cada fila antes de enviarla al libro para conservar el formato legible.
    $filas[] = [
        date('d/m/Y H:i', strtotime((string)$r['fecha_hora'])),
        (string)$r['usuario'],
        (string)$r['accion'],
        (string)$r['modulo'],
        (string)$r['resultado'],
        (string)$r['descripcion'],
        (string)$r['ip_origen'],
    ];
}

try {
    $xlsx = crearExcelXlsx(
        ['Fecha', 'Usuario', 'Acción', 'Módulo', 'Resultado', 'Descripción', 'IP'],
        $filas,
        [
            'titulo' => 'SISTEMA DE TUTORÍAS UPDS - REPORTE DE AUDITORÍA',
            'desde' => $desde !== '' ? $desde : 'TODAS',
            'hasta' => $hasta !== '' ? $hasta : 'TODAS',
            'generado' => date('Y-m-d H:i:s'),
        ]
    );
} catch (Throwable $e) {
    error_log('Error al generar Excel de auditoría: ' . $e->getMessage());
    http_response_code(500);
    exit('No fue posible generar el archivo Excel.');
}

$nombreArchivo = 'auditoria_' . ($desde !== '' ? $desde : 'todos') . '_' . ($hasta !== '' ? $hasta : 'todos') . '.xlsx';
$nombreArchivo = preg_replace('/[^A-Za-z0-9_.-]/', '_', $nombreArchivo) ?: 'auditoria.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . filesize($xlsx));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');

readfile($xlsx);
@unlink($xlsx);
exit;
