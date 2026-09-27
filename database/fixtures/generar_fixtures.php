<?php
/**
 * GENERADOR DE ARCHIVOS DE PRUEBA (fixtures)
 * ---------------------------------------------------------------------
 * Produce los archivos fisicos de prueba que despues se cargan en la
 * base de datos y en uploads/. Usa la misma clase PDF del sistema
 * (includes/pdf.php) para que los documentos generados sean identicos en
 * formato a los que produce la aplicacion.
 *
 * Ejecutar dentro del contenedor web:
 *     docker exec tutorias_web php /var/www/html/database/fixtures/generar_fixtures.php
 *
 * Las imagenes JPEG y el .docx los produce el script hermano
 * generar_binarios.ps1 porque requieren compresion ZIP y un codificador
 * de imagen que no estan disponibles en el PHP de este contenedor.
 */

$raiz = dirname(__DIR__, 2);
require_once $raiz . '/includes/pdf.php';

$base = __DIR__;

foreach (['pdfs', 'imagenes', 'documentos', 'padron', 'negativos'] as $sub) {
    $ruta = $base . '/' . $sub;
    if (!is_dir($ruta) && !@mkdir($ruta, 0775, true) && !is_dir($ruta)) {
        fwrite(STDERR, "No se pudo crear el directorio {$sub}\n");
        exit(1);
    }
}

$creados = [];
$fallidos = [];

/** Registra el resultado de cada archivo generado. */
function registrar(string $sub, string $nombre, string $ruta, array &$creados, array &$fallidos): void
{
    if (!is_file($ruta) || filesize($ruta) < 1) {
        $fallidos[] = $sub . '/' . $nombre;
        return;
    }
    $creados[] = [
        'sub'   => $sub,
        'arch'  => $sub . '/' . $nombre,
        'bytes' => filesize($ruta),
    ];
}

/* ------------------------------------------------------------------ */
/* PDF                                                                 */
/* ------------------------------------------------------------------ */

/**
 * Documento generico con encabezado, bloques de datos y pie institucional.
 */
function pdf_documento(string $titulo, string $subtitulo, array $bloques, array $parrafos = []): string
{
    $pdf = new PdfDocumento($titulo, 'Sistema de Tutorias Academicas UPDS');
    $derecha = PDF_ANCHO - PDF_MARGEN;

    $y = $pdf->posicionY();
    $pdf->textoFijo('UNIVERSIDAD PRIVADA DE SANTA CRUZ DE LA SIERRA', 12, PDF_MARGEN, $y, 'F2');
    $pdf->textoDerecha('Modalidad de Grado', 9, $derecha);
    $pdf->moverY($y - 13);
    $pdf->texto('Sede Tarija - Bolivia', 8.5, 'F3');
    $pdf->regla(1.1);

    $pdf->texto($titulo, 14, 'F2');
    $pdf->espacio(3);
    $pdf->texto($subtitulo, 9.5, 'F3');
    $pdf->espacio(8);

    foreach ($bloques as $etiqueta => $valor) {
        $pdf->asegurarEspacio(20);
        $yFila = $pdf->posicionY();
        $lineas = PdfDocumento::ajustar((string) $valor, $pdf->anchoUtil() - 150, 9.5);
        $pdf->textoFijo((string) $etiqueta, 9.5, PDF_MARGEN, $yFila, 'F2');
        $pdf->textoFijo($lineas[0], 9.5, PDF_MARGEN + 150, $yFila);
        $desplazado = 0.0;
        foreach (array_slice($lineas, 1) as $extra) {
            $desplazado -= 15;
            $pdf->asegurarEspacio(16);
            $pdf->textoFijo($extra, 9.5, PDF_MARGEN + 150, $yFila + $desplazado);
        }
        $pdf->moverY($yFila - (16.5 + $desplazado));
    }

    foreach ($parrafos as $parrafo) {
        $pdf->espacio(6);
        $pdf->parrafo($parrafo, 10);
    }

    $pdf->pie($derecha, [
        'Documento generado para pruebas del sistema. Datos ficticios.',
        'Universidad Privada de Santa Cruz de la Sierra - Sede Tarija',
    ]);

    return $pdf->salida();
}

$matricula = '2023-1180';
$estudiante = 'Maria Fernanda Quisbert Antezana';
$ru = '71234567';
$carrera = 'Ingenieria de Sistemas';

$evidencias = [
    'evidencia_reunion_01.pdf' => [
        'titulo'    => 'Evidencia de Reunion de Seguimiento',
        'subtitulo' => 'Acta de la sesion de tutoria N. 1 - ' . $matricula,
        'bloques'   => [
            'Estudiante'      => $estudiante,
            'Registro Univ.'  => $ru,
            'Carrera'         => $carrera,
            'Docente tutor'   => 'Ing. Luis Fernando Mamani Ortiz',
            'Fecha'           => '12 de marzo de 2026',
            'Horario'         => '18:00 a 19:30',
            'Modalidad'       => 'Trabajo de grado, modalidad Virtual',
            'Asistencia'     => 'Asistio el estudiante en su totalidad',
        ],
        'parrafos' => [
            'Se revisaron los avances del primer capitulo del trabajo de grado. '
            . 'El estudiante informo sobre la definicion del problema de investigacion '
            . 'y el estado de la revision bibliografica preliminar.',
            'El docente tutor orienta al estudiante a delimitar el alcance tematico, '
            . 'a seleccionar al menos cinco fuentes academicas indexadas y a mantener '
            . 'un registro semanal de avance.',
            'Quedan comprometidos: entrega de un indice tentativo del marco teorico '
            . 'para la proxima reunion y lectura de los dos articulos sugeridos.',
        ],
    ],
    'evidencia_reunion_02.pdf' => [
        'titulo'    => 'Evidencia de Reunion de Seguimiento',
        'subtitulo' => 'Acta de la sesion de tutoria N. 2 - ' . $matricula,
        'bloques'   => [
            'Estudiante'      => $estudiante,
            'Registro Univ.'  => $ru,
            'Docente tutor'   => 'Ing. Luis Fernando Mamani Ortiz',
            'Fecha'           => '26 de marzo de 2026',
            'Horario'         => '18:00 a 19:30',
            'Modalidad'       => 'Trabajo de grado, modalidad Virtual',
            'Asistencia'     => 'Asistio con retraso de cinco minutos',
        ],
        'parrafos' => [
            'El estudiante presento el indice tentativo del marco teorico, que fue '
            . 'observado por el docente tutor. Se corrigieron los nombres de las '
            . 'fuentes y se sugirio incorporar una fuente en ingles del area.',
            'Se acordo reformular dos objetivos especificos para que sean '
            . 'verificables con los instrumentos de evaluacion previstos.',
        ],
    ],
];

foreach ($evidencias as $nombre => $datos) {
    $ruta = $base . '/pdfs/' . $nombre;
    $ok = @file_put_contents($ruta, pdf_documento(
        $datos['titulo'],
        $datos['subtitulo'],
        $datos['bloques'],
        $datos['parrafos']
    ));
    registrar('pdfs', $nombre, $ruta, $creados, $fallidos);
}

$comprobantes = [
    'comprobante_pago_01.pdf' => ['monto' => '1.850,00 Bs', 'codigo' => 'TAR-448120397', 'fecha' => '14 de marzo de 2026', 'banco' => 'Banco Nacional de Bolivia'],
    'comprobante_pago_02.pdf' => ['monto' => '3.200,00 Bs', 'codigo' => 'TAR-771903485', 'fecha' => '02 de abril de 2026', 'banco' => 'Banco Mercantil Santa Cruz'],
];

foreach ($comprobantes as $nombre => $c) {
    $ruta = $base . '/pdfs/' . $nombre;
    $ok = @file_put_contents($ruta, pdf_documento(
        'Comprobante de Pago - Modalidad de Grado',
        'Recibo electronico de inscripcion a la modalidad de grado',
        [
            'Estudiante'        => $estudiante,
            'Registro Univ.'    => $ru,
            'Carrera'           => $carrera,
            'Concepto'          => 'Inscripcion a la modalidad de grado, gestion 2026',
            'Monto total'        => $c['monto'],
            'Codigo de control' => $c['codigo'],
            'Banco'             => $c['banco'],
            'Fecha de pago'     => $c['fecha'],
        ],
        [
            'Este comprobante se emite unicamente para fines de prueba del sistema '
            . 'de tutorias y no constituye un documento fiscal valido.',
        ]
    ));
    registrar('pdfs', $nombre, $ruta, $creados, $fallidos);
}

$expedientes = [
    'expediente_certificado_notas.pdf' => [
        'subtitulo' => 'Certificado academico de notas - gestion 2025',
        'bloques'   => [
            'Estudiante'     => $estudiante,
            'Registro Univ.' => $ru,
            'Carrera'        => $carrera,
            'Periodo'        => 'Primer semestre de 2025',
            'Materias'       => 'Programacion I (88), Calculo I (90), Algebra (85), '
                              . 'Estructuras de Datos (92), Fisica I (78), Ingles Tecnico (95)',
            'Promedio'       => '88,00 / 100',
            'Estado'         => 'Titulo academico de Bachiller en Ciencias Naturales adjudicado',
        ],
        'parrafos' => [
            'Por la presente se certifica que el estudiante supradio cursó las '
            . 'asignaturas correspondientes al plan de estudios vigente.',
        ],
    ],
    'expediente_constancia_matricula.pdf' => [
        'subtitulo' => 'Constancia de matricula - gestion 2026',
        'bloques'   => [
            'Estudiante'     => $estudiante,
            'Registro Univ.' => $ru,
            'Carrera'        => $carrera,
            'Gestion'        => '2026',
            'Modalidad'      => 'Modalidad de grado habilitada',
        ],
        'parrafos' => [
            'Se certifica que el estudiante se encuentra matriculado y activo en la '
            . 'modalidad de grado de la carrera durante la gestion 2026.',
        ],
    ],
];

foreach ($expedientes as $nombre => $datos) {
    $ruta = $base . '/pdfs/' . $nombre;
    @file_put_contents($ruta, pdf_documento(
        'Documento de Expediente Academico',
        $datos['subtitulo'],
        $datos['bloques'],
        $datos['parrafos']
    ));
    registrar('pdfs', $nombre, $ruta, $creados, $fallidos);
}

/* ------------------------------------------------------------------ */
/* PNG                                                                 */
/* ------------------------------------------------------------------ */

/**
 * Escribe un PNG truecolor built a mano: no hay GD en el contenedor.
 * Dibuja un fondo blanco con marco, encabezado y barras que simulan texto.
 */
function escribir_png(string $ruta, int $ancho, int $alto, string $etiqueta): void
{
    $crcTable = [];
    for ($n = 0; $n < 256; $n++) {
        $c = $n;
        for ($k = 0; $k < 8; $k++) {
            $c = ($c & 1) ? (0xEDB88320 ^ ($c >> 1)) : ($c >> 1);
        }
        $crcTable[$n] = $c & 0xFFFFFFFF;
    }
    $crc = static function (string $s) use (&$crcTable): int {
        $c = 0xFFFFFFFF;
        for ($i = 0, $n = strlen($s); $i < $n; $i++) {
            $c = $crcTable[($c ^ ord($s[$i])) & 0xFF] ^ ($c >> 8);
        }
        return ($c ^ 0xFFFFFFFF) & 0xFFFFFFFF;
    };

    $px = [];
    for ($y = 0; $y < $alto; $y++) {
        $fila = [];
        for ($x = 0; $x < $ancho; $x++) {
            if ($y < 4 || $y >= $alto - 5 || $x < 4 || $x >= $ancho - 5) {
                $fila[] = "\x5a\x6b\x7a";
                continue;
            }
            $fila[] = "\xff\xff\xff";
        }
        $px[] = $fila;
    }

    $dibujarBarra = static function (int $y0, int $x0, int $largo, int $grosor, string $rgb) use (&$px, $ancho, $alto) {
        for ($y = $y0; $y < $y0 + $grosor && $y < $alto; $y++) {
            for ($x = $x0; $x < $x0 + $largo && $x < $ancho; $x++) {
                if ($y > 4 && $y < $alto - 5 && $x > 4 && $x < $ancho - 5) {
                    $px[$y][$x] = $rgb;
                }
            }
        }
    };

    $dibujarBarra(30, 60, (int) ($ancho * 0.6), 16, "\x1f\x3a\x5f");
    $dibujarBarra(58, 60, (int) ($ancho * 0.35), 8, "\x8a\x8a\x8a");

    $yLinea = 110;
    for ($i = 0; $i < 9; $i++) {
        $largo = (int) ($ancho * (0.62 - ($i % 4) * 0.07));
        $dibujarBarra($yLinea, 70, $largo, 6, "\x3a\x3a\x3a");
        $yLinea += 26;
    }

    $dibujarBarra($yLinea + 24, 60, (int) ($ancho * 0.45), 5, "\x00\x66\x99");
    $yLinea += 70;

    for ($i = 0; $i < 6; $i++) {
        $dibujarBarra($yLinea, 70, (int) ($ancho * (0.58 - ($i % 3) * 0.08)), 5, "\x55\x55\x55");
        $yLinea += 22;
    }

    $chunk = static function (string $tipo, string $datos) use ($crc): string {
        return pack('N', strlen($datos)) . $tipo . $datos
            . pack('N', $crc($tipo . $datos));
    };

    $ihdr = pack('NN', $ancho, $alto) . chr(8) . chr(2) . chr(0) . chr(0) . chr(0);

    $raw = '';
    foreach ($px as $fila) {
        $raw .= chr(0) . implode('', $fila);
    }

    $png = "\x89PNG\r\n\x1a\n"
        . $chunk('IHDR', $ihdr)
        . $chunk('IDAT', gzcompress($raw, 9))
        . $chunk('IEND', '');

    file_put_contents($ruta, $png);
}

$imagenesPng = [
    'evidencia_reunion_01.png' => 'Evidencia de reunion de tutoria No. 1',
    'evidencia_reunion_02.png' => 'Evidencia de reunion de tutoria No. 2',
    'comprobante_pago_01.png'  => 'Comprobante de pago de modalidad de grado',
    'comprobante_pago_02.png'  => 'Recibo electronico de inscripcion 2026',
];

foreach ($imagenesPng as $nombre => $etiqueta) {
    $ruta = $base . '/imagenes/' . $nombre;
    escribir_png($ruta, 850, 1100, $etiqueta);
    registrar('imagenes', $nombre, $ruta, $creados, $fallidos);
}

/* ------------------------------------------------------------------ */
/* CSV del padron                                                     */
/* ------------------------------------------------------------------ */

$encabezado = 'registro_universitario;paterno;materno;nombre;correo;carrera;semestre;materias';

$carrerasReales = [
    'Ingenieria de Sistemas',
    'Contaduria Publica',
    'Administracion de Empresas',
    'Ingenieria Electronica',
    'Comunicacion Social',
];

$filasPadron = [
    ['90000101', 'Quisbert', 'Antezana', 'Maria Fernanda', 'mquisbert.90000101@upds.edu.bo', 'Ingenieria de Sistemas', '8', '64'],
    ['90000102', 'Guarachi', 'Mamani', 'Carlos Andres', 'cguarachi.90000102@upds.edu.bo', 'Ingenieria de Sistemas', '8', '61'],
    ['90000103', 'Chirve', 'Salvatierra', 'Lucia Paz', 'lchirve.90000103@upds.edu.bo', 'Contaduria Publica', '7', '58'],
    ['90000104', 'Menacho', 'Rojas', 'Sebastian', 'smenacho.90000104@upds.edu.bo', 'Administracion de Empresas', '9', '70'],
    ['90000105', 'Ortiz', 'Villca', 'Camila Andrea', 'cortiz.90000105@upds.edu.bo', 'Ingenieria Electronica', '6', '49'],
    ['90000106', 'Aguilar', 'Perez', 'Diego Raul', 'daguilar.90000106@upds.edu.bo', 'Comunicacion Social', '8', '66'],
    ['90000107', 'Banegas', 'Suarez', 'Daniela', 'dbanegas.90000107@upds.edu.bo', 'Ingenieria de Sistemas', '7', '55'],
    ['90000108', 'Choque', 'Mamani', 'Kevin', 'kchoque.90000108@upds.edu.bo', 'Contaduria Publica', '9', '72'],
];

$lineasPadron = [$encabezado];
foreach ($filasPadron as $f) {
    $lineasPadron[] = implode(';', $f);
}
$rutaPadron = $base . '/padron/padron_cohorte_2026_1.csv';
file_put_contents($rutaPadron, implode("\r\n", $lineasPadron) . "\r\n");
registrar('padron', 'padron_cohorte_2026_1.csv', $rutaPadron, $creados, $fallidos);

$lineasComa = [str_replace(';', ',', $encabezado)];
foreach ($filasPadron as $f) {
    $lineasComa[] = implode(',', $f);
}
$rutaComa = $base . '/padron/padron_delimitador_coma.csv';
file_put_contents($rutaComa, implode("\n", $lineasComa) . "\n");
registrar('padron', 'padron_delimitador_coma.csv', $rutaComa, $creados, $fallidos);

/* ------------------------------------------------------------------ */
/* Casos negativos                                                    */
/* ------------------------------------------------------------------ */

/** PDF con la estructura cortada a mitad: sin trailer, sin %%EOF ni startxref. */
$rutaTruncado = $base . '/negativos/pdf_truncado.pdf';
$pdfBueno = pdf_documento('Documento truncado', 'Prueba de integridad', ['Campo' => 'Valor'], []);
$corte = (int) (strlen($pdfBueno) * 0.55);
file_put_contents($rutaTruncado, substr($pdfBueno, 0, $corte));
registrar('negativos', 'pdf_truncado.pdf', $rutaTruncado, $creados, $fallidos);

/** PDF con todo el cuerpo pero sin el trailer %%EOF. */
$rutaSinEof = $base . '/negativos/pdf_sin_eof.pdf';
$sinEof = preg_replace('/%%EOF\s*$/s', '', $pdfBueno);
file_put_contents($rutaSinEof, (string) $sinEof);
registrar('negativos', 'pdf_sin_eof.pdf', $rutaSinEof, $creados, $fallidos);

/** PDF declarado pero que en realidad es un PNG renombrado. */
$rutaMascarado = $base . '/negativos/png_renombrado_como_pdf.pdf';
file_put_contents($rutaMascarado, file_get_contents($base . '/imagenes/evidencia_reunion_01.png'));
registrar('negativos', 'png_renombrado_como_pdf.pdf', $rutaMascarado, $creados, $fallidos);

/** PDF de mas de 5 MB, por encima del limite de negocio y del servidor. */
$rutaGrande = $base . '/negativos/pdf_supera_limite_5mb.pdf';
$pdfValido = pdf_documento('Documento sobredimensionado', 'Prueba de limite de tamano', ['Campo' => 'Valor'], []);
$relleno = str_repeat("%linea de relleno para superar el limite de cinco megabytes 0123456789\n", 90000);
file_put_contents($rutaGrande, $pdfValido . "\n" . $relleno);
registrar('negativos', 'pdf_supera_limite_5mb.pdf', $rutaGrande, $creados, $fallidos);

/** Archivo vacio con extension permitida. */
$rutaVacio = $base . '/negativos/pdf_vacio.pdf';
file_put_contents($rutaVacio, '');
registrar('negativos', 'pdf_vacio.pdf', $rutaVacio, $creados, $fallidos);

/** CSV con correos invalidos, RUs repetidos y carrera inexistente. */
$rutaCsvMalo = $base . '/negativos/csv_correos_invalidos.csv';
$csvMalo = [
    $encabezado,
    '90000901;Perez;Suarez;Juan Carlos;correo-sin-arroba.com;Ingenieria de Sistemas;8;60',
    '90000902;Perez;Suarez;Ana;   ;Ingenieria de Sistemas;8;60',
    '90000903;Perez;Suarez;Luis;luis@;Carrera Inexistente;8;60',
    '90000904;Perez;Suarez;Marta;marta@@doble.com;Ingenieria de Sistemas;8;60',
    '90000905;Perez;Suarez;Pedro;pedro@upds.edu.bo;Ingenieria de Sistemas;8;60',
];
file_put_contents($rutaCsvMalo, implode("\r\n", $csvMalo) . "\r\n");
registrar('negativos', 'csv_correos_invalidos.csv', $rutaCsvMalo, $creados, $fallidos);

/** CSV sin las columnas requeridas por el importador. */
$rutaCsvSinColumnas = $base . '/negativos/csv_sin_columnas_requeridas.csv';
$csvSinColumnas = [
    'codigo;nombre_completo;direccion',
    'A001;Juan Perez;Calle 1',
    'A002;Maria Lopez;Calle 2',
];
file_put_contents($rutaCsvSinColumnas, implode("\r\n", $csvSinColumnas) . "\r\n");
registrar('negativos', 'csv_sin_columnas_requeridas.csv', $rutaCsvSinColumnas, $creados, $fallidos);

/** XML con el padron: el sistema solo admite .csv o .txt y debe rechazarlo. */
$rutaXml = $base . '/negativos/padron_en_xml.xml';
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
    . "<padron>\n"
    . "  <estudiante>\n"
    . "    <registro_universitario>90000801</registro_universitario>\n"
    . "    <nombre>Juan Carlos</nombre>\n"
    . "    <paterno>Perez</paterno>\n"
    . "    <correo>jcperez@upds.edu.bo</correo>\n"
    . "    <carrera>Ingenieria de Sistemas</carrera>\n"
    . "  </estudiante>\n"
    . "</padron>\n";
file_put_contents($rutaXml, $xml);
registrar('negativos', 'padron_en_xml.xml', $rutaXml, $creados, $fallidos);

/* ------------------------------------------------------------------ */

echo "ARCHIVOS GENERADOS\n";
echo str_repeat('-', 62) . "\n";
$total = 0;
foreach ($creados as $c) {
    printf("  %-46s %10d bytes\n", $c['arch'], $c['bytes']);
    $total += $c['bytes'];
}
echo str_repeat('-', 62) . "\n";
printf("Total: %d archivos, %d bytes\n\n", count($creados), $total);

if ($fallidos) {
    echo "FALLIDOS:\n";
    foreach ($fallidos as $f) {
        echo "  - {$f}\n";
    }
    exit(1);
}

echo "Sin fallidos.\n";
