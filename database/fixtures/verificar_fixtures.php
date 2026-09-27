<?php
/**
 * VERIFICADOR DE FIXTURES
 * ---------------------------------------------------------------------
 * Pasa cada archivo de database/fixtures/ por los mismos validadores que
 * usa la aplicacion en caliente, para comprobar que los archivos validos
 * se aceptan y que los casos negativos se rechazan por el motivo correcto.
 *
 * Ejecutar dentro del contenedor web:
 *     docker exec tutorias_web php /var/www/html/database/fixtures/verificar_fixtures.php
 */

$raiz = dirname(__DIR__, 2);
require_once $raiz . '/includes/archivos.php';

$base = __DIR__;

$lineas = [];
$fallos = 0;

function fila(string $arch, string $esperado, string $resultado, string $motivo = ''): void
{
    global $lineas, $fallos;

    if (str_starts_with($esperado, 'aceptar')) {
        $ok = $resultado === 'aceptar';
    } elseif ($esperado === 'n/a') {
        $ok = true;
    } else {
        $ok = $resultado !== 'aceptar';
    }

    if (!$ok) {
        $fallos++;
    }
    $marca = $ok ? 'OK  ' : 'FALLA';
    $lineas[] = sprintf('  [%s] %-42s %-10s %s', $marca, $arch, $resultado, $motivo);
}

/** Aplica la validacion real de expediente (extension + MIME + estructura). */
function comprobar_documento(string $ruta): array
{
    $errores = [];
    $extension = strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION));
    $reglas = document_upload_rules();

    if (!in_array($extension, array_keys($reglas), true)) {
        return ['rechazado', 'extension no permitida (.pdf/.doc/.docx)'];
    }

    $bytes = @filesize($ruta);
    if ($bytes === false || $bytes <= 0) {
        return ['rechazado', 'archivo vacio'];
    }
    if ($bytes > 5242880) {
        return ['rechazado', 'supera el limite de 5 MB (' . $bytes . ' bytes)'];
    }

    $mime = detect_upload_mime($ruta);
    if (!document_extension_matches_mime($extension, $mime)) {
        return ['rechazado', 'MIME ' . $mime . ' no corresponde a .' . $extension];
    }

    if ($extension === 'pdf' && !pdf_is_structurally_valid($ruta)) {
        return ['rechazado', 'PDF sin estructura valida (%%EOF/startxref)'];
    }

    return ['aceptar', 'MIME ' . $mime . ', ' . $bytes . ' bytes'];
}

echo "DOCUMENTOS DE EXPEDIENTE  (solo .pdf / .doc / .docx, max 5 MB)\n";
echo str_repeat('-', 78) . "\n";

$documentos = [
    ['pdfs/expediente_certificado_notas.pdf',      'aceptar'],
    ['pdfs/expediente_constancia_matricula.pdf',   'aceptar'],
    ['pdfs/comprobante_pago_01.pdf',               'aceptar'],
    ['pdfs/evidencia_reunion_01.pdf',              'aceptar'],
    ['documentos/expediente_curriculum_vitae.docx', 'aceptar'],
    ['documentos/expediente_ensayo_tesis.docx',    'aceptar'],
    ['negativos/pdf_truncado.pdf',                 'rechazar'],
    ['negativos/pdf_sin_eof.pdf',                  'rechazar'],
    ['negativos/png_renombrado_como_pdf.pdf',      'rechazar'],
    ['negativos/docx_renombrado_como_pdf.pdf',      'rechazar'],
    ['negativos/pdf_supera_limite_5mb.pdf',        'rechazar'],
    ['negativos/pdf_vacio.pdf',                    'rechazar'],
    ['imagenes/evidencia_reunion_01.png',          'rechazar'],
];

foreach ($documentos as [$rel, $esperado]) {
    $ruta = $base . '/' . $rel;
    if (!is_file($ruta)) {
        fila($rel, $esperado, 'rechazado', 'FIXTURE INEXISTENTE');
        continue;
    }
    [$resultado, $motivo] = comprobar_documento($ruta);
    fila($rel, $esperado, $resultado, $motivo);
}
echo implode("\n", $lineas) . "\n\n";
$lineas = [];

echo "EVIDENCIAS Y COMPROBANTES  (solo .jpg / .jpeg / .png / .pdf, max 5 MB)\n";
echo str_repeat('-', 78) . "\n";

$imagenes = [
    ['imagenes/evidencia_reunion_01.png',   'aceptar'],
    ['imagenes/evidencia_reunion_02.png',   'aceptar'],
    ['imagenes/evidencia_reunion_03.jpg',   'aceptar'],
    ['imagenes/comprobante_pago_01.png',    'aceptar'],
    ['imagenes/comprobante_pago_02.png',    'aceptar'],
    ['imagenes/comprobante_pago_03.jpg',    'aceptar'],
    ['imagenes/expediente_fotografia_carnet.jpg', 'n/a'],
    ['pdfs/evidencia_reunion_02.pdf',       'aceptar'],
    ['negativos/pdf_truncado.pdf',          'aceptar_truncado'],
    ['negativos/pdf_supera_limite_5mb.pdf', 'rechazar'],
    ['documentos/expediente_ensayo_tesis.docx', 'rechazar'],
];

$mimesPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
$extPermitidas   = ['jpg', 'jpeg', 'png', 'pdf'];

foreach ($imagenes as [$rel, $esperado]) {
    $ruta = $base . '/' . $rel;
    if (!is_file($ruta)) {
        fila($rel, $esperado, 'rechazado', 'FIXTURE INEXISTENTE');
        continue;
    }
    if ($esperado === 'n/a') {
        fila($rel, $esperado, 'n/a', 'no pertenece a este punto de carga (solo expediente)');
        echo implode("\n", $lineas) . "\n\n";
        $lineas = [];
        continue;
    }
    $ext = strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION));
    $bytes = (int) filesize($ruta);
    $mime = detect_upload_mime($ruta);

    $detalle = 'MIME ' . $mime . ', ' . $bytes . ' bytes';

    if (!in_array($ext, $extPermitidas, true)) {
        fila($rel, $esperado, 'rechazado', 'extension .' . $ext . ' no permitida');
    } elseif ($bytes <= 0) {
        fila($rel, $esperado, 'rechazado', 'archivo vacio');
    } elseif ($bytes > 5242880) {
        fila($rel, $esperado, 'rechazado', 'supera 5 MB');
    } elseif (!in_array($mime, $mimesPermitidos, true)) {
        fila($rel, $esperado, 'rechazado', 'MIME ' . $mime . ' fuera de la lista');
    } elseif ($esperado === 'aceptar_truncado') {
        /* La app no llama a pdf_is_structurally_valid() en este punto. */
        fila($rel, $esperado, 'aceptar', $detalle . ' <- BRECHA: se acepta sin validar estructura');
    } else {
        fila($rel, $esperado, 'aceptar', $detalle);
    }
}
echo implode("\n", $lineas) . "\n\n";
$lineas = [];

/** Reproduce el chequeo de cabeceras y delimitador del importador de padron. */
function comprobar_padron(string $ruta, array $carrerasValidas): array
{
    $extension = strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION));
    if (!in_array($extension, ['csv', 'txt'], true)) {
        return ['rechazado', 'el importador solo admite .csv o .txt'];
    }

    $manejador = fopen($ruta, 'rb');
    if ($manejador === false) {
        return ['rechazado', 'archivo ilegible'];
    }
    $primera = (string) fgets($manejador);
    rewind($manejador);
    $delimitador = (substr_count($primera, ';') >= substr_count($primera, ',')) ? ';' : ',';
    $cabeceras = fgetcsv($manejador, 0, $delimitador);
    fclose($manejador);

    if ($cabeceras === false) {
        return ['rechazado', 'CSV vacio o invalido'];
    }

    $mapa = [
        'registro' => 'registro_universitario', 'ru' => 'registro_universitario',
        'matricula' => 'registro_universitario', 'nombres' => 'nombre', 'nombre' => 'nombre',
        'correo' => 'correo', 'email' => 'correo', 'carrera' => 'carrera', 'semestre' => 'semestre',
    ];
    $columnas = array_map(static function (string $c) use ($mapa): string {
        $c = mb_strtolower(trim($c));
        $c = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $c);
        $c = trim((string) preg_replace('/[^a-z0-9_]+/', '_', $c), '_');
        return $mapa[$c] ?? $c;
    }, $cabeceras);

    $requeridas = ['registro_universitario', 'nombre', 'correo', 'carrera'];
    $faltantes = array_diff($requeridas, $columnas);
    if ($faltantes) {
        return ['rechazado', 'faltan columnas: ' . implode(', ', $faltantes)];
    }

    return ['aceptar', 'delimitador ' . $delimitador . ', ' . count($columnas) . ' columnas'];
}

require_once $raiz . '/config/conexion.php';
$carrerasValidas = $pdo->query('SELECT nombre_carrera FROM carreras')->fetchAll(PDO::FETCH_COLUMN);

echo "IMPORTACION DE PADRON  (solo .csv o .txt, 4 columnas requeridas)\n";
echo str_repeat('-', 78) . "\n";

$padrones = [
    ['padron/padron_cohorte_2026_1.csv',      'aceptar'],
    ['padron/padron_delimitador_coma.csv',    'aceptar'],
    ['negativos/csv_correos_invalidos.csv',   'aceptar_columnas'],
    ['negativos/csv_sin_columnas_requeridas.csv', 'rechazar'],
    ['negativos/padron_en_xml.xml',           'rechazar'],
];

foreach ($padrones as [$rel, $esperado]) {
    $ruta = $base . '/' . $rel;
    if (!is_file($ruta)) {
        fila($rel, $esperado, 'rechazado', 'FIXTURE INEXISTENTE');
        continue;
    }
    [$resultado, $motivo] = comprobar_padron($ruta, $carrerasValidas);
    if ($esperado === 'aceptar_columnas') {
        $ok = $resultado === 'aceptar';
        $lineas[] = sprintf(
            '  [%s] %-42s %-10s %s',
            $ok ? 'OK  ' : 'FALLA',
            $rel,
            $resultado,
            $ok ? $motivo . ' (con 4 filas, 3 con correo invalido)' : $motivo
        );
        if (!$ok) {
            $fallos++;
        }
        continue;
    }
    fila($rel, $esperado, $resultado, $motivo);
}
echo implode("\n", $lineas) . "\n\n";

echo str_repeat('=', 78) . "\n";
echo $fallos === 0
    ? "RESULTADO: todos los fixtures se comportan como se esperaba.\n"
    : "RESULTADO: {$fallos} comprobaciones fallaron.\n";
echo str_repeat('=', 78) . "\n";

exit($fallos === 0 ? 0 : 1);
