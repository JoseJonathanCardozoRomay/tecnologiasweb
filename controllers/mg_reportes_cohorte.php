<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgReporteModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();
$cohorteDefault = 0;
foreach ($cohortes as $c) {
    if ($c['estado'] === 'abierta') {
        $cohorteDefault = (int) $c['id_cohorte_mg'];
        break;
    }
}
if ($cohorteDefault === 0 && $cohortes) {
    $cohorteDefault = (int) $cohortes[0]['id_cohorte_mg'];
}

$cohorteId = (int) ($_GET['cohorte'] ?? $cohorteDefault);
$reporteModel = new MgReporteModel($pdo);
$filas = $cohorteId > 0 ? $reporteModel->consolidado($cohorteId) : [];
$cohorteActual = $cohorteId > 0 ? $cohorteModel->obtenerPorId($cohorteId) : null;

if (isset($_GET['exportar']) && $_GET['exportar'] === '1') {
    $nombre = 'reporte_cohorte_' . ($cohorteActual['nombre_periodo'] ?? $cohorteId) . '_' . date('Ymd_His');

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nombre . '.csv"');

    echo "\xEF\xBB\xBF"; // BOM UTF-8 para Excel
    $csv = fopen('php://output', 'w');
    fputcsv($csv, [
        'EXPEDIENTE', 'RU', 'ESTUDIANTE', 'CARRERA', 'MODALIDAD', 'COHORTE',
        'TUTOR', 'ESTADO EXPEDIENTE', 'DEFENSA', 'NOTA FINAL', 'RESULTADO',
    ], ';');
    foreach ($filas as $f) {
        fputcsv($csv, [
            $f['id_expediente_mg'],
            $f['registro_universitario'],
            $f['estudiante_apellido'] . ' ' . $f['estudiante_nombre'],
            $f['nombre_carrera'],
            $f['modalidad_nombre'],
            $cohorteActual['nombre_periodo'] ?? '',
            $f['tutor_nombre'] ? $f['tutor_apellido'] . ' ' . $f['tutor_nombre'] : '',
            $f['estado_expediente'],
            $f['fecha_defensa'] ? $f['fecha_defensa'] . ' ' . $f['hora_inicio'] : '',
            $f['nota_final'] !== null ? $f['nota_final'] : '',
            $f['resultado'] ?: '',
        ], ';');
    }
    fclose($csv);
    exit;
}

$kpi = [
    'total' => count($filas),
    'con_tutor' => 0,
    'sin_tutor' => 0,
    'programadas' => 0,
    'evaluadas' => 0,
    'aprobados' => 0,
    'reprobados' => 0,
];
$sumaNotas = 0.0;
$nNotas = 0;
foreach ($filas as $f) {
    if ($f['tutor_nombre']) {
        $kpi['con_tutor']++;
    } else {
        $kpi['sin_tutor']++;
    }
    if ($f['estado_defensa'] === 'programada') {
        $kpi['programadas']++;
    } elseif ($f['estado_defensa'] === 'evaluada') {
        $kpi['evaluadas']++;
    }
    if ($f['resultado'] === 'aprobado') {
        $kpi['aprobados']++;
    } elseif ($f['resultado'] === 'reprobado') {
        $kpi['reprobados']++;
    }
    if ($f['nota_final'] !== null) {
        $sumaNotas += (float) $f['nota_final'];
        $nNotas++;
    }
}
$kpi['promedio'] = $nNotas > 0 ? round($sumaNotas / $nNotas, 2) : null;

require_once __DIR__ . '/../views/mg/reportes/cohorte.php';