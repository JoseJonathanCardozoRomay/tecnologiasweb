<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DocumentoExpedienteModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/archivos.php';

$docModel = new DocumentoExpedienteModel($pdo);
$id_tutoria = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$errores = [];
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tutoriaModel = new TutoriaModel($pdo);
$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? '');

/**
 * Devuelve el expediente de una tutoría en JSON para que el panel se cargue
 * sin recargar la página completa.
 */
function responder_expediente_json(PDO $pdo, DocumentoExpedienteModel $docModel, TutoriaModel $tutoriaModel, int $id_tutoria): void
{
    $seleccionada = null;
    foreach ($tutoriaModel->obtenerTodas() as $t) {
        if ((int) $t['id_tutoria'] === $id_tutoria && !empty($t['id_modalidad'])) {
            $seleccionada = $t;
            break;
        }
    }

    if ($seleccionada === null) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'La tutoría indicada no existe o no es de Modalidad de Grado.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ids = (new NotificationModel($pdo))->usuariosDeTutoria($id_tutoria);

    $documentos = [];
    foreach ($docModel->obtenerPorTutoria($id_tutoria) as $d) {
        $ruta = (string) $d['ruta_archivo'];
        $verificacion = verify_stored_file($ruta, 'uploads/expedientes');
        $documentos[] = [
            'id' => (int) $d['id_documento'],
            'nombre' => (string) $d['nombre_original'],
            'descripcion' => (string) ($d['descripcion'] ?? ''),
            'fecha' => date('d/m/Y H:i', strtotime((string) $d['fecha'])),
            'destinatario' => trim((string) $d['destinatario_nombre'] . ' ' . $d['destinatario_apellido']),
            'esPdf' => strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION)) === 'pdf',
            'disponible' => !empty($verificacion['ok']),
            'motivo' => (string) ($verificacion['motivo'] ?? ''),
            'bytes' => (int) ($verificacion['bytes'] ?? 0),
            'urlVer' => '/controllers/expediente_documento_descargar.php?id=' . (int) $d['id_documento'] . '&ver=inline',
            'urlDescargar' => '/controllers/expediente_documento_descargar.php?id=' . (int) $d['id_documento'] . '&ver=descargar',
        ];
    }

    $nombreEstudiante = trim((string) ($seleccionada['estudiante_nombre'] ?? '') . ' ' . (string) ($seleccionada['estudiante_apellido'] ?? ''));
    $nombreTutor = trim('Prof. ' . (string) ($seleccionada['tutor_nombre'] ?? '') . ' ' . (string) ($seleccionada['tutor_apellido'] ?? ''));

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'tutoria' => [
            'id' => (int) $seleccionada['id_tutoria'],
            'estudiante' => $nombreEstudiante,
            'tutor' => $nombreTutor,
            'estado' => (string) $seleccionada['estado'],
        ],
        'destinatarios' => [
            ['id' => (int) ($ids['id_usuario_estudiante'] ?? 0), 'etiqueta' => 'Estudiante: ' . $nombreEstudiante],
            ['id' => (int) ($ids['id_usuario_tutor'] ?? 0), 'etiqueta' => 'Tutor: ' . $nombreTutor],
        ],
        'documentos' => $documentos,
        'limiteBytes' => $docModel->tamanioMaximo(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (in_array($accion, ['documentos', 'subir'], true) && es_peticion_json()) {
    if ($accion === 'subir') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_error_archivo(405, 'Método no permitido.');
        }
        csrf_validar();
    }

    if ($id_tutoria <= 0) {
        responder_error_archivo(400, 'Selecciona una tutoría.');
    }

    if ($accion === 'documentos') {
        responder_expediente_json($pdo, $docModel, $tutoriaModel, $id_tutoria);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $id_operador = (int) ($_SESSION['id_usuario'] ?? 0);

    if ($id_tutoria <= 0) {
        $errores[] = 'Selecciona una tutoría.';
    }

    $validacion = ['lista' => [], 'mime' => '', 'extension' => ''];
    if (empty($_FILES['archivo'] ?? null)) {
        $errores[] = 'Debes seleccionar un archivo .doc, .docx o .pdf.';
    } else {
        $validacion = validate_uploaded_document($_FILES['archivo'], $docModel->tamanioMaximo());
        foreach ($validacion['lista'] as $fallo) {
            $errores[] = $fallo;
        }
    }

    $extension = $validacion['extension'];

    if (empty($errores)) {
        $archivo = $_FILES['archivo'];

        $dir = __DIR__ . '/../uploads/expedientes';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $base = basename($archivo['name'], '.' . $extension);
        $base = preg_replace('/[^A-Za-z0-9_\- ]/', '', $base);
        $base = $base === '' ? 'documento' : $base;
        $nombreArchivo = $id_tutoria . '_' . time() . '_' . str_replace(' ', '_', $base) . '.' . $extension;

        $destinatario = (int) ($_POST['destinatario'] ?? 0);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        if ($destinatario <= 0) {
            $errores[] = 'Indica el destinatario del documento.';
        }

        if (empty($errores) && !move_uploaded_file($archivo['tmp_name'], $dir . '/' . $nombreArchivo)) {
            $errores[] = 'No se pudo guardar el archivo en el servidor.';
        }

        if (empty($errores)) {
            $ruta = 'uploads/expedientes/' . $nombreArchivo;
            $docModel->registrar($id_tutoria, $id_operador, $destinatario, $archivo['name'], $ruta, $descripcion);

            $historial = new HistorialModel($pdo);
            $historial->registrar($id_operador, 'DOCUMENTO_ENVIADO', 'Documento "' . $archivo['name'] . '" enviado al expediente de la tutoría #' . $id_tutoria . '.');

            $notifModel = new NotificationModel($pdo);
            $notifModel->crear($destinatario, 'DOCUMENTO_RECIBIDO', 'Recibiste un documento en el expediente de tu tutoría #' . $id_tutoria . '.', '/controllers/expediente_documentos.php?id=' . $id_tutoria, $id_operador);

            if ($accion === 'subir' && es_peticion_json()) {
                responder_expediente_json($pdo, $docModel, $tutoriaModel, $id_tutoria);
            }

            flash_set('success', 'Documento subido y notificado correctamente.');
            header('Location: expediente_documentos.php?id=' . $id_tutoria);
            exit;
        }
    }

    if (!empty($errores)) {
        if ($accion === 'subir' && es_peticion_json()) {
            responder_error_archivo(422, implode(' ', array_values(array_unique($errores))));
        }

        flash_set('error', implode(' ', array_values(array_unique($errores))));
        header('Location: expediente_documentos.php?id=' . $id_tutoria);
        exit;
    }
}

$tutoriasMG = array_values(array_filter(
    $tutoriaModel->obtenerTodas(),
    fn($t) => !empty($t['id_modalidad'])
));

$tutoriaSeleccionada = null;
$documentos = [];
if ($id_tutoria > 0) {
    foreach ($tutoriasMG as $t) {
        if ((int) $t['id_tutoria'] === $id_tutoria) {
            $tutoriaSeleccionada = $t;
            break;
        }
    }
    $documentos = $docModel->obtenerPorTutoria($id_tutoria);
}

include __DIR__ . '/../views/tutorias/expediente.php';