<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar', 'tutor', 'estudiante']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DocumentoExpedienteModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/archivos.php';

$id_documento = (int) ($_GET['id'] ?? 0);
if ($id_documento <= 0) {
    responder_error_archivo(400, 'Solicitud inválida: falta el identificador del documento.');
}

$documento = (new DocumentoExpedienteModel($pdo))->obtenerPorId($id_documento);
if (!$documento) {
    responder_error_archivo(404, 'El documento solicitado no existe.');
}

$rol = $_SESSION['rol'] ?? '';
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

if ($rol === 'tutor' || $rol === 'estudiante') {
    $participa = false;
    $stmt = $pdo->prepare(
        "SELECT 1
         FROM tutorias t
         INNER JOIN estudiantes e ON e.id_estudiante = t.id_estudiante
         INNER JOIN tutores tu ON tu.id_tutor = t.id_tutor
         WHERE t.id_tutoria = :id_tutoria
           AND (e.id_usuario = :id_usuario OR tu.id_usuario = :id_usuario)
         LIMIT 1"
    );
    $stmt->execute([
        ':id_tutoria'  => (int) $documento['id_tutoria'],
        ':id_usuario'  => $id_usuario,
    ]);
    $participa = (bool) $stmt->fetchColumn();

    if (!$participa) {
        http_response_code(403);
        if (es_peticion_json()) {
            responder_error_archivo(403, 'No tenés permiso para acceder a este documento.');
        }
        require __DIR__ . '/../views/errores/403.php';
        exit;
    }
}

$ruta = trim((string) ($documento['ruta_archivo'] ?? ''));
if ($ruta === '') {
    responder_error_archivo(404, 'Este documento no tiene archivo adjunto.');
}

$verificacion = verify_stored_file($ruta, 'uploads/expedientes');

$nombreOriginal = (string) ($documento['nombre_original'] ?? 'documento');
$sufijo = (string) pathinfo($ruta, PATHINFO_EXTENSION);
$nombre = pathinfo($nombreOriginal, PATHINFO_FILENAME) . ($sufijo !== '' ? '.' . $sufijo : '');

$inline = ($_GET['ver'] ?? 'inline') !== 'descargar';

servir_archivo_almacenado($verificacion, $nombre !== '' ? $nombre : 'documento', $inline);
