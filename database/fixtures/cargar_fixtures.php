<?php
/**
 * CARGA DE FIXTURES EN LA BASE DE DATOS Y EN uploads/
 * ---------------------------------------------------------------------
 * Copia los archivos de database/fixtures/ a las carpetas de destino con el
 * mismo convenio de nombres que usa la aplicacion y registra las filas
 * correspondientes a traves de los modelos, de modo que los datos quedan
 * exactamente como si hubieran subido por la interfaz.
 *
 * Ejecutar dentro del contenedor web:
 *     docker exec tutorias_web php /var/www/html/database/fixtures/cargar_fixtures.php
 *
 * El script es idempotente en cuanto a los archivos: si ya existe una copia
 * con el mismo nombre base no la vuelve a escribir. Las filas se agregan de
 * nuevo en cada ejecucion, por eso se recomienda correrlo una sola vez.
 */

$raiz = dirname(__DIR__, 2);
require_once $raiz . '/config/conexion.php';
require_once $raiz . '/models/ComprobanteModel.php';
require_once $raiz . '/models/ReunionModel.php';
require_once $raiz . '/models/DocumentoExpedienteModel.php';
require_once $raiz . '/models/MgPadronModel.php';
require_once $raiz . '/models/NotificationModel.php';
require_once $raiz . '/models/HistorialModel.php';
require_once $raiz . '/models/EstudianteModel.php';

$fixtures = __DIR__;
$registros = [];
$errores = [];

function nota(string $tipo, string $detalle): void
{
    global $registros;
    $registros[] = '  [' . $tipo . '] ' . $detalle;
}

function fallo(string $detalle): void
{
    global $errores;
    $errores[] = '  [ERROR] ' . $detalle;
}

/** Copia un fixture a uploads/ con el convenio de nombres del punto de carga. */
function copiar(string $origen, string $destino): bool
{
    $carpeta = dirname($destino);
    if (!is_dir($carpeta) && !@mkdir($carpeta, 0775, true) && !is_dir($carpeta)) {
        return false;
    }
    return @copy($origen, $destino);
}

$marca = time();
$sufijo = bin2hex(random_bytes(4));

echo "CARGA DE FIXTURES\n";
echo str_repeat('=', 74) . "\n";

/* ---------------------------------------------------------------- */
/* 1. Comprobantes de pago (uploads/comprobantes)                   */
/* ---------------------------------------------------------------- */

$idAdmin = 1;
$estudiantes = $pdo->query(
    'SELECT e.id_estudiante, e.id_usuario, e.registro_universitario
       FROM estudiantes e
      WHERE e.acceso_mg_desbloqueado = 1
      ORDER BY e.id_estudiante
      LIMIT 3'
)->fetchAll();

$comprobantes = [
    ['pdfs/comprobante_pago_01.pdf',    1850.00, '2026-03-14', 'comprobante_pago_01'],
    ['imagenes/comprobante_pago_01.png', 3200.00, '2026-04-02', 'comprobante_pago_02'],
    ['imagenes/comprobante_pago_03.jpg', 2500.50, '2026-04-18', 'comprobante_pago_03'],
];

$modeloComprobante = new ComprobanteModel($pdo);
$notificaciones = new NotificationModel($pdo);

foreach ($comprobantes as $i => [$rel, $monto, $fecha, $etiqueta]) {
    $origen = $fixtures . '/' . $rel;
    if (!is_file($origen) || !isset($estudiantes[$i])) {
        fallo('comprobante ' . $etiqueta . ': fixture o estudiante no disponible');
        continue;
    }

    $ext = strtolower((string) pathinfo($origen, PATHINFO_EXTENSION));
    $nombre = 'comprobante_mg_' . ($marca + $i) . '_' . $sufijo . '.' . $ext;
    $destino = $raiz . '/uploads/comprobantes/' . $nombre;

    if (!copiar($origen, $destino)) {
        fallo('no se pudo copiar ' . $rel);
        continue;
    }

    try {
        $id = $modeloComprobante->registrar(
            (int) $estudiantes[$i]['id_estudiante'],
            $monto,
            $fecha,
            'uploads/comprobantes/' . $nombre
        );

        $historial = new HistorialModel($pdo);
        $historial->registrar(
            (int) $estudiantes[$i]['id_usuario'],
            'COMPROBANTE_MG',
            'Registro de comprobante de pago para Modalidad de Grado.'
        );

        /* Mismo destino que el controlador: todo el personal de administracion. */
        $notificadas = 0;
        foreach ($notificaciones->idsUsuariosAdministracion() as $dest) {
            $notificaciones->crear(
                (int) $dest,
                'COMPROBANTE_MG',
                'Nuevo comprobante de pago de Modalidad de Grado pendiente de validacion.',
                'mg_comprobantes_listar.php',
                (int) $estudiantes[$i]['id_usuario']
            );
            $notificadas++;
        }

        nota('comprobante', sprintf(
            '#%d | estudiante %s | %s Bs | %d notificacion(es) | %s',
            $id,
            $estudiantes[$i]['registro_universitario'],
            number_format($monto, 2, ',', '.'),
            $notificadas,
            $nombre
        ));
    } catch (Throwable $e) {
        fallo('comprobante ' . $etiqueta . ': ' . $e->getMessage());
    }
}

/* ---------------------------------------------------------------- */
/* 2. Reuniones con evidencia (uploads/evidencias)                 */
/* ---------------------------------------------------------------- */

$idTutoriaEvidencia = 21;
$modeloReunion = new ReunionModel($pdo);

$reuniones = [
    ['imagenes/evidencia_reunion_01.png', '2026-03-12', '18:00:00', '19:30:00', 'Aula Virtual 3 - Meet', 'Se reviso el avance del primer capitulo y la bibliografia preliminar.'],
    ['pdfs/evidencia_reunion_01.pdf',     '2026-03-26', '18:00:00', '19:30:00', 'Aula Virtual 3 - Meet', 'Se presento el indice tentativo del marco teorico y se corrigieron las fuentes.'],
    ['imagenes/evidencia_reunion_03.jpg', '2026-04-09', '18:30:00', '20:00:00', 'Biblioteca Central', 'Revision del marco teorico y de los objetivos especificados del trabajo.'],
];

foreach ($reuniones as $i => [$rel, $fecha, $hi, $hf, $lugar, $obs]) {
    $origen = $fixtures . '/' . $rel;
    if (!is_file($origen)) {
        fallo('reunion ' . $rel . ': fixture inexistente');
        continue;
    }

    $ext = strtolower((string) pathinfo($origen, PATHINFO_EXTENSION));
    $nombre = 'evidencia_' . ($marca + $i) . '_' . $sufijo . '.' . $ext;
    $destino = $raiz . '/uploads/evidencias/' . $nombre;

    if (!copiar($origen, $destino)) {
        fallo('no se pudo copiar ' . $rel);
        continue;
    }

    try {
        $id = $modeloReunion->registrar(
            $idTutoriaEvidencia,
            $fecha,
            $hi,
            $hf,
            $lugar,
            $obs,
            'uploads/evidencias/' . $nombre
        );
        nota('reunion', sprintf('#%d | tutoria %d | %s %s-%s | %s', $id, $idTutoriaEvidencia, $fecha, substr($hi, 0, 5), substr($hf, 0, 5), $nombre));
    } catch (Throwable $e) {
        fallo('reunion ' . $rel . ': ' . $e->getMessage());
    }
}

/* ---------------------------------------------------------------- */
/* 3. Documentos de expediente (uploads/expedientes)                */
/* ---------------------------------------------------------------- */

$modeloDocumento = new DocumentoExpedienteModel($pdo);

/* La vista de expediente (expediente_documentos.php?id=N) solo responde JSON
   si la tutoria tiene modalidad de grado asignada, asi que los documentos se
   cuelgan de una tutoria 'grado' con id_modalidad, no de una de apoyo. */
$idTutoriaExpediente = 3;

$documentos = [
    ['pdfs/expediente_certificado_notas.pdf',       'Certificado de notas', 'Documento academico que acredita el historial de asignaturas del estudiante.'],
    ['pdfs/expediente_constancia_matricula.pdf',    'Constancia de matricula', 'Constancia de estar matriculado y activo en la modalidad de grado.'],
    ['documentos/expediente_curriculum_vitae.docx', 'Curriculum vitae', 'Curriculum actualizado del estudiante.'],
    ['documentos/expediente_ensayo_tesis.docx',     'Ensayo de tesis', 'Anteproyecto del trabajo de grado.'],
];

/* El destinatario es el tutor de la tutoria; el origen, el estudiante. */
$pares = $pdo->query(
    'SELECT tut.id_usuario AS id_usuario_tutor, e.id_usuario AS id_usuario_estudiante
       FROM tutorias t
       JOIN tutores tut ON tut.id_tutor = t.id_tutor
       JOIN estudiantes e ON e.id_estudiante = t.id_estudiante
      WHERE t.id_tutoria = ' . (int) $idTutoriaExpediente . '
      LIMIT 1'
)->fetch();

if (!$pares) {
    fallo('no se pudo resolver el par tutor/estudiante de la tutoria ' . $idTutoriaExpediente);
} else {
    foreach ($documentos as $i => [$rel, $descripcion, $detalle]) {
        $origen = $fixtures . '/' . $rel;
        if (!is_file($origen)) {
            fallo('documento ' . $rel . ': fixture inexistente');
            continue;
        }

        $ext = strtolower((string) pathinfo($origen, PATHINFO_EXTENSION));
        $base = preg_replace('/[^A-Za-z0-9_\- ]/', '', basename($origen, '.' . $ext));
        $base = $base === '' ? 'documento' : $base;
        $nombre = $idTutoriaExpediente . '_' . ($marca + $i) . '_' . str_replace(' ', '_', $base) . '.' . $ext;
        $destino = $raiz . '/uploads/expedientes/' . $nombre;

        if (!copiar($origen, $destino)) {
            fallo('no se pudo copiar ' . $rel);
            continue;
        }

        try {
            $id = $modeloDocumento->registrar(
                $idTutoriaExpediente,
                (int) $pares['id_usuario_estudiante'],
                (int) $pares['id_usuario_tutor'],
                basename($origen),
                'uploads/expedientes/' . $nombre,
                $descripcion . '. ' . $detalle
            );
            nota('documento', sprintf('#%d | tutoria %d | %-9s | %s', $id, $idTutoriaExpediente, '.' . $ext, $nombre));
        } catch (Throwable $e) {
            fallo('documento ' . $rel . ': ' . $e->getMessage());
        }
    }
}

/* ---------------------------------------------------------------- */
/* 4. Padron de estudiantes (importacion masiva)                     */
/* ---------------------------------------------------------------- */

$idCohorte = 2; /* 2026-I, cohorte abierta */
$modeloPadron = new MgPadronModel($pdo);

/** Mismo criterio de cabeceras que el controlador de importacion. */
function normalizarCabecera(string $celda): string
{
    $celda = mb_strtolower(trim($celda));
    $celda = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $celda);
    $sin = trim((string) preg_replace('/[^a-z0-9_]+/', '_', $celda), '_');
    $mapa = [
        'registro' => 'registro_universitario', 'ru' => 'registro_universitario',
        'matricula' => 'registro_universitario', 'apellido_paterno' => 'paterno',
        'paterno' => 'paterno', 'apellido_materno' => 'materno', 'materno' => 'materno',
        'apellidos' => 'paterno', 'apellido' => 'paterno', 'nombres' => 'nombre',
        'nombre' => 'nombre', 'correo' => 'correo', 'email' => 'correo',
        'carrera' => 'carrera', 'semestre' => 'semestre', 'materias' => 'materias',
    ];
    return $mapa[$sin] ?? $sin;
}

$rutaPadron = $fixtures . '/padron/padron_cohorte_2026_1.csv';
$manejador = fopen($rutaPadron, 'rb');
$primera = (string) fgets($manejador);
rewind($manejador);
$delimitador = (substr_count($primera, ';') >= substr_count($primera, ',')) ? ';' : ',';
$cabeceras = fgetcsv($manejador, 0, $delimitador);
$columnas = array_map('normalizarCabecera', $cabeceras);

$filas = [];
while (($fila = fgetcsv($manejador, 0, $delimitador)) !== false) {
    if (count($fila) === 1 && trim($fila[0] ?? '') === '') {
        continue;
    }
    $registro = [];
    foreach ($columnas as $i => $col) {
        $registro[$col] = $fila[$i] ?? '';
    }
    $filas[] = $registro;
}
fclose($manejador);

try {
    $resumen = $modeloPadron->importarFilas($filas, $idCohorte, $idAdmin);
    $creados = (int) ($resumen['creados'] ?? 0);
    $actualizados = (int) ($resumen['actualizados'] ?? 0);
    $erroresFila = $resumen['errores'] ?? [];

    nota('padron', sprintf(
        "cohorte %d (2026-I) | %d filas leidas | %d creados | %d actualizados | %d con error",
        $idCohorte,
        count($filas),
        $creados,
        $actualizados,
        count($erroresFila)
    ));

    foreach (array_slice($erroresFila, 0, 8) as $detalle) {
        nota('padron-error', (string) $detalle);
    }
} catch (Throwable $e) {
    fallo('importacion del padron: ' . $e->getMessage());
}

/* ---------------------------------------------------------------- */

echo implode("\n", $registros) . "\n";
echo str_repeat('=', 74) . "\n";

if ($errores) {
    echo "INCIDENCIAS:\n" . implode("\n", $errores) . "\n";
    echo str_repeat('=', 74) . "\n";
    echo count($errores) . " incidencia(s).\n";
    exit(1);
}

echo "Carga completada sin incidencias.\n";
