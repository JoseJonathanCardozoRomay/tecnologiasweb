<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReunionModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../includes/pdf_reporte.php';
require_once __DIR__ . '/../includes/doc_reporte.php';

$id_reunion = (int) ($_GET['id_reunion'] ?? 0);
$formato = strtolower(trim((string) ($_GET['formato'] ?? 'pdf')));
if (!in_array($formato, ['pdf', 'doc'], true)) {
    $formato = 'pdf';
}

if ($id_reunion <= 0) {
    http_response_code(400);
    exit('Solicitud inválida.');
}

$reunionModel = new ReunionModel($pdo);
$reunion = $reunionModel->obtenerPorId($id_reunion);
if (!$reunion) {
    http_response_code(404);
    exit('La reunión solicitada no existe.');
}

$rol = $_SESSION['rol'] ?? '';
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

if ($rol === 'tutor' && (int) ($reunion['id_usuario_tutor'] ?? 0) !== $id_usuario) {
    http_response_code(403);
    require __DIR__ . '/../views/errores/403.php';
    exit;
}
if ($rol === 'estudiante' && (int) ($reunion['id_usuario_estudiante'] ?? 0) !== $id_usuario) {
    http_response_code(403);
    require __DIR__ . '/../views/errores/403.php';
    exit;
}
if (!in_array($rol, ['tutor', 'estudiante', 'administrador', 'auxiliar'], true)) {
    http_response_code(403);
    require __DIR__ . '/../views/errores/403.php';
    exit;
}

$seguimiento = $reunionModel->obtenerSeguimiento($id_reunion);
if (!$seguimiento) {
    http_response_code(409);
    exit('Primero registra el seguimiento de la reunión para poder generar el informe final.');
}

$registradoPor = trim(($seguimiento['nombre_registro'] ?? '') . ' ' . ($seguimiento['apellido_registro'] ?? ''));

$camposIdentificacion = [
    'Reunión N.º' => (string) $id_reunion,
    'Tutoría N.º' => (string) ($reunion['id_tutoria'] ?? '—'),
    'Fecha' => date('d/m/Y', strtotime($reunion['fecha'])),
    'Horario' => substr((string) $reunion['hora_inicio'], 0, 5) . ' a ' . substr((string) $reunion['hora_fin'], 0, 5) . ' horas',
    'Lugar o enlace' => (string) ($reunion['lugar_o_enlace'] ?: 'No indicado'),
];

$camposAsistencia = [
    'Asistencia del estudiante' => ReunionModel::etiquetaAsistencia($seguimiento['asistencia']),
    'Cumplimiento de la reunión' => ReunionModel::etiquetaCumplimiento($seguimiento['cumplimiento']),
];

$camposRegistro = [
    'Fecha de registro del seguimiento' => date('d/m/Y H:i', strtotime($seguimiento['fecha_registro'])),
];
if (!empty($seguimiento['fecha_actualizacion'])) {
    $camposRegistro['Última actualización'] = date('d/m/Y H:i', strtotime($seguimiento['fecha_actualizacion']));
}
$camposRegistro['Registrado por'] = $registradoPor !== '' ? $registradoPor : 'Sin registro de usuario';
$camposRegistro['Generado el'] = date('d/m/Y H:i');

$bloques = [
    ['seccion', 'Identificación de la reunión'],
    ['campos', $camposIdentificacion],
    ['seccion', 'Asistencia y cumplimiento'],
    ['campos', $camposAsistencia],
    ['seccion', 'Temas tratados en la reunión'],
    ['parrafo', (string) ($reunion['observaciones'] ?: 'Sin registro de temas.')],
    ['seccion', 'Observaciones del seguimiento'],
    ['parrafo', (string) ($seguimiento['observaciones'] ?: 'Sin observaciones registradas.')],
    ['seccion', 'Compromisos acordados'],
    ['parrafo', (string) ($seguimiento['compromisos'] ?: 'Sin compromisos registrados.')],
    ['seccion', 'Registro'],
    ['campos', $camposRegistro],
];

$titulo = 'Informe de Seguimiento de Reunión';
$subtitulo = 'Modalidad de Tutorías · Universidad Privada Domingo Savio · Sede Tarija';
$cierre = 'Documento generado automáticamente por el Sistema de Tutorías de la Universidad Privada Domingo Savio.';

$extension = $formato === 'doc' ? 'doc' : 'pdf';
$nombreArchivo = 'informe_reunion_' . $id_reunion . '_' . date('Ymd_His') . '.' . $extension;
$rutaRelativa = 'uploads/informes/' . $nombreArchivo;
$rutaAbsoluta = __DIR__ . '/../' . $rutaRelativa;

$carpeta = __DIR__ . '/../uploads/informes/';
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0775, true);
}

if ($formato === 'doc') {
    $doc = new DocReporte($titulo, $subtitulo);
    foreach ($bloques as $bloque) {
        $doc->agregar($bloque[0], $bloque[1]);
    }
    $doc->cerrar($cierre);
    $contenido = $doc->salida();
} else {
    $pdf = new PdfReporte();
    $pdf->titulo($titulo);
    $pdf->subtitulo($subtitulo);
    foreach ($bloques as $bloque) {
        if ($bloque[0] === 'seccion') {
            $pdf->seccion($bloque[1]);
        } elseif ($bloque[0] === 'campos') {
            foreach ($bloque[1] as $etiqueta => $valor) {
                $pdf->campo($etiqueta, $valor);
            }
        } else {
            $pdf->parrafo($bloque[1]);
        }
    }
    $pdf->espacio(10);
    $pdf->parrafo($cierre);
    $contenido = $pdf->salida();
}

if (file_put_contents($rutaAbsoluta, $contenido) === false) {
    http_response_code(500);
    exit('No se pudo guardar el informe en el servidor.');
}
$reunionModel->guardarInforme($id_reunion, $rutaRelativa);

(new HistorialModel($pdo))->registrar(
    $id_usuario,
    'INFORME_REUNION',
    'Generación del informe final de la reunión #' . $id_reunion . ' (' . strtoupper($formato) . ').'
);

header('Content-Type: ' . ($formato === 'doc'
    ? 'application/msword'
    : 'application/pdf'));
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . filesize($rutaAbsoluta));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($rutaAbsoluta);
exit;
