<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReporteMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirPermiso(
    'mg.reportes.exportar',
    '../index.php'
);

$idCohorte = filter_input(
    INPUT_GET,
    'cohorte',
    FILTER_VALIDATE_INT
) ?: null;

$idModalidad = filter_input(
    INPUT_GET,
    'modalidad',
    FILTER_VALIDATE_INT
) ?: null;

$etapa = $_GET['etapa'] ?? '';
$estado = $_GET['estado'] ?? '';

if (
    !in_array(
        $etapa,
        ['', 'previa', 'mg1', 'mg2', 'finalizado'],
        true
    )
) {
    $etapa = '';
}

if (
    !in_array(
        $estado,
        [
            '',
            'activo',
            'aprobado',
            'reprobado',
            'abandono',
            'retirado'
        ],
        true
    )
) {
    $estado = '';
}

$modeloReporte = new ReporteMgModel($pdo);

$expedientes = $modeloReporte->listarGeneral(
    $idCohorte,
    $idModalidad,
    $etapa,
    $estado
);

/**
 * Evita que Excel interprete contenido como una fórmula.
 */
function protegerCeldaCsv(string $valor): string
{
    if (
        $valor !== ''
        && in_array(
            $valor[0],
            ['=', '+', '-', '@'],
            true
        )
    ) {
        return "'" . $valor;
    }

    return $valor;
}

$nombreArchivo = 'reporte-modalidades-grado-'
    . date('Y-m-d-His')
    . '.csv';

header(
    'Content-Type: text/csv; charset=UTF-8'
);
header(
    'Content-Disposition: attachment; filename="'
    . $nombreArchivo
    . '"'
);
header('X-Content-Type-Options: nosniff');

$salida = fopen('php://output', 'wb');

// BOM para compatibilidad con Excel.
fwrite($salida, "\xEF\xBB\xBF");

fputcsv(
    $salida,
    [
        'Estudiante',
        'Modalidad',
        'Cohorte',
        'Etapa',
        'Fecha de inicio',
        'Avance',
        'Tutor',
        'Estado'
    ],
    ';'
);

foreach ($expedientes as $expediente) {
    $fila = [
        $expediente['nombre']
            . ' '
            . $expediente['apellido'],
        $expediente['modalidad'],
        $expediente['cohorte'],
        strtoupper($expediente['etapa_actual']),
        $expediente['fecha_inicio'],
        $expediente['ultimo_avance'] !== null
            ? $expediente['ultimo_avance'] . '%'
            : 'Sin datos',
        $expediente['tutor_actual']
            ?: 'Sin tutor',
        ucfirst($expediente['estado'])
    ];

    $fila = array_map(
        fn($valor): string =>
            protegerCeldaCsv((string) $valor),
        $fila
    );

    fputcsv(
        $salida,
        $fila,
        ';'
    );
}

fclose($salida);
exit;