<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReunionModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

$id_reunion = (int) ($_GET['id'] ?? 0);
if ($id_reunion <= 0) {
    http_response_code(400);
    exit('Solicitud inválida.');
}

$reunion = (new ReunionModel($pdo))->obtenerPorId($id_reunion);
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

if ($rol === 'estudiante') {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario($id_usuario);
    if (!$estudiante) {
        http_response_code(403);
        require __DIR__ . '/../views/errores/403.php';
        exit;
    }
    $tutoriaId = (int) ($reunion['id_tutoria'] ?? 0);
    $esEstudianteDeTutoria = false;
    if ($tutoriaId > 0) {
        // Verificar tanto en tutoria_estudiantes (grupo) como en tutorias.id_estudiante (individual)
        $stmt = $pdo->prepare(
            "SELECT 1 FROM (
                SELECT id_estudiante FROM tutoria_estudiantes WHERE id_tutoria = :id_tutoria1
                UNION
                SELECT id_estudiante FROM tutorias WHERE id_tutoria = :id_tutoria2
            ) AS t WHERE id_estudiante = :id_estudiante"
        );
        $stmt->execute([':id_tutoria1' => $tutoriaId, ':id_tutoria2' => $tutoriaId, ':id_estudiante' => $estudiante['id_estudiante']]);
        $esEstudianteDeTutoria = (bool) $stmt->fetchColumn();
    }
    if (!$esEstudianteDeTutoria) {
        http_response_code(403);
        require __DIR__ . '/../views/errores/403.php';
        exit;
    }
}

if (!in_array($rol, ['tutor', 'estudiante', 'administrador', 'auxiliar'], true)) {
    http_response_code(403);
    require __DIR__ . '/../views/errores/403.php';
    exit;
}

$ruta = trim((string) ($reunion['evidencia_url'] ?? ''));
if ($ruta === '') {
    http_response_code(404);
    exit('Esta reunión no tiene evidencia adjunta.');
}

$base = realpath(__DIR__ . '/../uploads/evidencias');
if ($base === false) {
    http_response_code(404);
    exit('No se pudo resolver la carpeta de evidencias.');
}

$archivo = realpath(__DIR__ . '/../' . ltrim($ruta, '/'));
if ($archivo === false || !is_file($archivo) || strpos($archivo, $base . DIRECTORY_SEPARATOR) !== 0) {
    http_response_code(404);
    exit('El archivo de evidencia ya no está disponible.');
}

$mime = 'application/octet-stream';
$ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectado = finfo_file($finfo, $archivo);
    finfo_close($finfo);
    if ($detectado) {
        $mime = $detectado;
    }
} elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
    $mime = $ext === 'pdf' ? 'application/pdf' : 'image/' . $ext;
}

$nombre = 'evidencia_reunion_' . $id_reunion . '_' . date('Ymd') . ($ext !== '' ? '.' . $ext : '');

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($archivo));
header('Content-Disposition: inline; filename="' . str_replace('"', '', $nombre) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($archivo);
exit;
