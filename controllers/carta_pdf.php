<?php
/**
 * Genera y sirve el PDF de la Carta de Designación.
 *
 * El proyecto no guarda un archivo PDF de la carta (cartas_designacion solo
 * almacena datos), así que el documento se construye en el momento a partir de
 * la base de datos, se cachea en uploads/cartas y recién entonces se valida
 * con las mismas comprobaciones de integridad usadas para los adjuntos del
 * expediente antes de enviarlo al navegador.
 *
 * Ver: inline (ver=ver) o descarga adjunta (ver=descargar).
 */
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../includes/archivos.php';
require_once __DIR__ . '/../includes/pdf.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$id_carta = (int) ($_GET['id'] ?? 0);
$inline = ($_GET['ver'] ?? 'ver') !== 'descargar';

if ($id_carta <= 0) {
    responder_error_archivo(400, 'Solicitud incompleta: falta el identificador de la carta.');
}

$stmt = $pdo->prepare(
    'SELECT c.*,
            tut_nombre.nombre AS tutor_nombre, tut_nombre.apellido AS tutor_apellido,
            tut_nombre.correo AS tutor_correo, tut.especialidad AS tutor_especialidad,
            est_nombre.nombre AS estudiante_nombre, est_nombre.apellido AS estudiante_apellido,
            est.registro_universitario AS estudiante_ru,
            car.nombre_carrera AS carrera,
            m.nombre_materia, md.nombre AS modalidad_nombre,
            t.estado AS estado_tutoria, t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin
     FROM cartas_designacion c
     INNER JOIN tutorias t ON c.id_tutoria = t.id_tutoria
     INNER JOIN tutores tut ON c.id_tutor = tut.id_tutor
     INNER JOIN usuarios tut_nombre ON tut.id_usuario = tut_nombre.id_usuario
     INNER JOIN estudiantes est ON c.id_estudiante = est.id_estudiante
     INNER JOIN usuarios est_nombre ON est.id_usuario = est_nombre.id_usuario
     LEFT JOIN carreras car ON est.id_carrera = car.id_carrera
     INNER JOIN materias m ON t.id_materia = m.id_materia
     LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
     WHERE c.id_carta = :id
     LIMIT 1'
);
$stmt->execute([':id' => $id_carta]);
$carta = $stmt->fetch();

if (!$carta) {
    responder_error_archivo(404, 'La carta de designación solicitada no existe.');
}

/**
 * Solo el tutor destinatario de la carta y el personal administrativo pueden
 * verla: el estudiante recibe la misma información desde su panel.
 */
$rol = $_SESSION['rol'] ?? '';
$esDestinatario = false;
if ($rol === 'tutor') {
    $tutorPropio = (new TutorModel($pdo))->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
    $esDestinatario = $tutorPropio && (int) $tutorPropio['id_tutor'] === (int) $carta['id_tutor'];
}
$esPersonal = in_array($rol, ['administrador', 'auxiliar'], true);

if (!$esDestinatario && !$esPersonal) {
    responder_error_archivo(403, 'No tenés permiso para ver la carta de designación.');
}

$config = new ConfiguracionModel($pdo);
$institucion = mgDatosInstitucion($config);

/**
 * La copia cacheada se versiona con los datos que realmente se imprimen.
 * Si el tutor acepta o rechaza la carta (cambian estado, fecha_firma y
 * motivo_rechazo) el hash cambia y se genera un PDF nuevo, de modo que
 * nunca se sirve un documento obsoleto. Las versiones anteriores se purgan.
 */
$version = substr(
    hash('sha256', (string) json_encode([$carta, $institucion], JSON_UNESCAPED_UNICODE)),
    0,
    12
);

$prefijoCarta = 'carta_' . $id_carta . '_';
$rutaRelativa = 'uploads/cartas/' . $prefijoCarta . $version . '.pdf';
$rutaAbsoluta = __DIR__ . '/../' . $rutaRelativa;

/* Se reutiliza la copia cacheada solo si sigue siendo íntegra. */
$verificacion = verify_stored_file($rutaRelativa, 'uploads/cartas', 200);

if (empty($verificacion['ok'])) {
    $base = __DIR__ . '/../uploads/cartas';
    if (!is_dir($base) && !@mkdir($base, 0775, true) && !is_dir($base)) {
        responder_error_archivo(500, 'No se pudo preparar el directorio de cartas.');
    }

    $pdf = construir_pdf_carta($carta, $institucion);

    $temporal = $rutaAbsoluta . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (file_put_contents($temporal, $pdf) === false) {
        @unlink($temporal);
        responder_error_archivo(500, 'No se pudo generar el archivo PDF de la carta.');
    }
    if (!@rename($temporal, $rutaAbsoluta)) {
        @unlink($temporal);
        if (file_put_contents($rutaAbsoluta, $pdf) === false) {
            responder_error_archivo(500, 'No se pudo guardar el archivo PDF de la carta.');
        }
    }
    @chmod($rutaAbsoluta, 0644);

    /* Segunda validación sobre el archivo ya escrito en disco. */
    $verificacion = verify_stored_file($rutaRelativa, 'uploads/cartas', 200);
    if (empty($verificacion['ok'])) {
        @unlink($rutaAbsoluta);
        responder_error_archivo(
            500,
            'El PDF generado no superó la validación de integridad: ' . (string) $verificacion['motivo']
        );
    }

    /* Purga las versiones anteriores de esta misma carta. */
    carta_purgar_versiones($base, $prefijoCarta, $rutaAbsoluta);
}

servir_archivo_almacenado($verificacion, 'carta-designacion-' . $id_carta . '.pdf', $inline);

/* ------------------------------------------------------------------ */

/**
 * Elimina los PDF cacheados de la carta que ya no corresponden a la versión
 * vigente, para que el directorio no crezca de forma indefinida.
 */
function carta_purgar_versiones(string $base, string $prefijo, string $conservar): void
{
    $anteriores = glob($base . DIRECTORY_SEPARATOR . $prefijo . '*.pdf') ?: [];
    foreach ($anteriores as $archivo) {
        if ($archivo !== $conservar && is_file($archivo)) {
            @unlink($archivo);
        }
    }
}

/** Construye el PDF de la carta con los datos obtenidos de la base. */
function construir_pdf_carta(array $carta, array $institucion): string
{
    $universidad = $institucion['universidad'];
    $eslogan = $institucion['eslogan'];
    $telefono = $institucion['telefono'];
    $correo = $institucion['correo'];

    $tutor = trim(trim((string) ($carta['tutor_nombre'] ?? '')) . ' ' . trim((string) ($carta['tutor_apellido'] ?? '')));
    $estudiante = trim(trim((string) ($carta['estudiante_nombre'] ?? '')) . ' ' . trim((string) ($carta['estudiante_apellido'] ?? '')));
    $correlativo = 'CD-' . str_pad((string) $carta['id_carta'], 5, '0', STR_PAD_LEFT);
    $derecha = PDF_ANCHO - PDF_MARGEN;

    $pdf = new PdfDocumento('Carta de Designación ' . $correlativo, $universidad);

    /* Encabezado institucional */
    $yEncabezado = $pdf->posicionY();
    $pdf->textoFijo($universidad, 13, PDF_MARGEN, $yEncabezado, 'F2');
    $pdf->textoDerecha('Coordinación de Modalidad de Grado', 8.5, $derecha);
    $pdf->textoDerecha('Correlativo ' . $correlativo, 11, $derecha, 'F2');
    $pdf->moverY($yEncabezado - 12);
    $pdf->texto(trim($eslogan . ' · Sede Tarija'), 8, 'F3');
    $pdf->regla(1.2);

    /* Fecha y destinatario */
    $pdf->textoDerecha(carta_fecha_espanol((string) $carta['fecha_generacion']), 10, $derecha);
    $pdf->espacio(10);

    $pdf->parrafo(
        'Señor(a): ' . $estudiante
        . (!empty($carta['estudiante_ru']) ? ', con Registro Universitario ' . $carta['estudiante_ru'] : '')
        . (!empty($carta['carrera']) ? ', estudiante de la carrera ' . $carta['carrera'] : '')
        . '.',
        10.5
    );

    $pdf->texto('ASUNTO: DESIGNACIÓN DE DOCENTE TUTOR', 10.5, 'F2');
    $pdf->espacio(6);

    $pdf->parrafo(
        'La Coordinación de Modalidad de Grado de la ' . $universidad . ', en atención a la '
        . 'solicitud registrada y a la normativa vigente, tiene a bien designar a usted como docente '
        . 'tutor de la modalidad de grado que a continuación se detalla:',
        10.5
    );

    /* Datos de la designación */
    $horario = '-';
    if (!empty($carta['hora_inicio']) && !empty($carta['hora_fin'])) {
        $horario = substr((string) $carta['hora_inicio'], 0, 5) . ' a ' . substr((string) $carta['hora_fin'], 0, 5);
    }

    $filas = [
        'Correlativo' => $correlativo,
        'Docente tutor' => $tutor . (!empty($carta['tutor_correo']) ? ' (' . $carta['tutor_correo'] . ')' : ''),
        'Especialidad' => !empty($carta['tutor_especialidad']) ? $carta['tutor_especialidad'] : 'No registrada',
        'Estudiante' => $estudiante,
        'Materia' => (string) ($carta['nombre_materia'] ?? '-'),
        'Modalidad' => (string) ($carta['modalidad_nombre'] ?? 'Sin modalidad'),
        'Fecha de la tutoría' => carta_fecha_espanol((string) ($carta['fecha_tutoria'] ?? '')),
        'Horario' => $horario,
        'Estado de la carta' => carta_estado((string) $carta['estado']),
    ];

    foreach ($filas as $etiqueta => $valor) {
        $pdf->asegurarEspacio(20);
        $yFila = $pdf->posicionY();
        $lineas = PdfDocumento::ajustar((string) $valor, $pdf->anchoUtil() - 150, 9.5);
        $pdf->textoFijo($etiqueta, 9.5, PDF_MARGEN, $yFila, 'F2');
        $pdf->textoFijo($lineas[0], 9.5, PDF_MARGEN + 150, $yFila);
        $desplazado = 0.0;
        foreach (array_slice($lineas, 1) as $extra) {
            $desplazado -= 15;
            $pdf->asegurarEspacio(16);
            $pdf->textoFijo($extra, 9.5, PDF_MARGEN + 150, $yFila + $desplazado);
        }
        $pdf->moverY($yFila - (16.5 + $desplazado));
    }

    $pdf->espacio(4);

    $pdf->parrafo(
        'La designación se efectúa para el periodo académico vigente y queda sujeta a las '
        . 'obligaciones de la Ley Orgánica de Educación Superior y a las directrices de la '
        . 'Coordinación de Modalidad de Grado. La aceptación de esta carta deberá registrarse '
        . 'en el sistema dentro del plazo establecido.',
        10
    );

    if (!empty($carta['fecha_firma'])) {
        $pdf->parrafo('Carta aceptada por el docente el ' . carta_fecha_espanol((string) $carta['fecha_firma']) . '.', 10);
    }
    if (!empty($carta['motivo_rechazo'])) {
        $pdf->parrafo('Motivo de rechazo: ' . $carta['motivo_rechazo'], 10);
    }

    $pdf->parrafo('Sin otro particular, reciba un cordial saludo.', 10.5);

    /* Firmas */
    $pdf->espacio(34);
    $yFirma = $pdf->posicionY();
    $anchoFirma = $pdf->anchoUtil() / 2 - 20;
    $pdf->textoFijo(carta_primera_linea($tutor, $anchoFirma, 9.5, 'F2'), 9.5, PDF_MARGEN, $yFirma, 'F2');
    $pdf->textoFijo('DOCENTE TUTOR', 8, PDF_MARGEN, $yFirma - 12);
    $pdf->textoFijo('Coordinación de Modalidad de Grado', 9.5, PDF_MARGEN + $anchoFirma + 40, $yFirma, 'F2');
    $pdf->textoFijo('SECRETARÍA / COORDINACIÓN', 8, PDF_MARGEN + $anchoFirma + 40, $yFirma - 12);
    $pdf->moverY($yFirma - 5);
    $pdf->regla(0.7, '0.1 0.1 0.1', PDF_MARGEN, PDF_MARGEN + $anchoFirma, false);
    $pdf->regla(0.7, '0.1 0.1 0.1', PDF_MARGEN + $anchoFirma + 40, $derecha, false);

    /* Pie institucional, siempre al final de la última página utilizada. */
    $contacto = trim($telefono . ' · ' . $correo, ' ·');
    $pdf->pie($derecha, [
        trim($universidad . ' · ' . $eslogan),
        $contacto !== '' ? $contacto : null,
        'Documento generado electrónicamente por el Sistema de Tutorías. No requiere firma digital.',
    ]);

    return $pdf->salida();
}

function carta_primera_linea(string $texto, float $ancho, float $tamano, string $fuente): string
{
    $lineas = PdfDocumento::ajustar($texto, $ancho, $tamano, $fuente);

    return (string) $lineas[0];
}

function carta_fecha_espanol(string $fecha): string
{
    $fecha = substr(trim($fecha), 0, 10);
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $m)) {
        return $fecha === '' ? '-' : $fecha;
    }
    $meses = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];
    $mes = (int) $m[2];

    return (int) $m[3] . ' de ' . ($meses[$mes] ?? (string) $m[2]) . ' de ' . $m[1];
}

function carta_estado(string $estado): string
{
    return [
        'pendiente' => 'Pendiente de aceptación',
        'aceptada' => 'Aceptada por el docente',
        'rechazada' => 'Rechazada por el docente',
    ][$estado] ?? ucfirst($estado);
}
