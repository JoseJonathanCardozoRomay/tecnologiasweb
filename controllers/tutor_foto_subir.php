<?php
/**
 * Subida de foto de perfil del tutor.
 * Recibe un archivo image/jpeg, image/png o image/webp.
 * Lo guarda físicamente en assets/img/tutores/ con nombre único.
 * Retorna JSON con la ruta relativa para guardar en la BD.
 *
 * Reglas de seguridad:
 * - Tamaño máximo: 2MB
 * - Extensiones permitidas: jpg, jpeg, png, webp
 * - La carpeta destino DEBE existir (crearla si no existe, con permisos 775)
 * - El nombre del archivo usa: foto_{id_tutor}.{ext} para evitar colisiones
 * - NO aceptar archivos .svg (riesgo XSS si se sirven como imagen)
 * - Retornar JSON únicamente. Nunca imprimir HTML.
 */

require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';

header('Content-Type: application/json; charset=utf-8');

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$tutorModel = new TutorModel($pdo);
$tutor = $tutorModel->obtenerPorUsuario($idUsuario);

if (!$tutor) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No se pudo identificar el tutor.']);
    exit;
}

$idTutor = $tutor['id_tutor'];
$rutaBase = __DIR__ . '/../assets/img/tutores/';

// Crear carpeta si no existe
if (!is_dir($rutaBase)) {
    mkdir($rutaBase, 0775, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

// Verificar que se recibió un archivo
if (empty($_FILES['foto_file']) || $_FILES['foto_file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se recibió archivo o error en la subida.']);
    exit;
}

$archivo = $_FILES['foto_file'];
$nombreOriginal = $archivo['name'];
$tamano = $archivo['size'];
$tipoMime = $archivo['type'];
$tmpName = $archivo['tmp_name'];

// Validar tamaño (máximo 2MB)
if ($tamano > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'La imagen no puede superar 2MB.']);
    exit;
}

// Validar extensión por el nombre real (más confiable que mime type)
$extensiónPermitida = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
if (!in_array($ext, $extensiónPermitida, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Formato no permitido. Usa JPG, PNG o WebP.']);
    exit;
}

// Validar que es realmente una imagen (ignorar extensión falsa)
$infoImagen = @getimagesize($tmpName);
if ($infoImagen === false) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El archivo no es una imagen válida.']);
    exit;
}

// Validar que no sea SVG
if ($ext === 'svg') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'SVG no permitido por seguridad.']);
    exit;
}

// Generar nombre único: foto_{id_tutor}.{ext}
$nuevoNombre = 'foto_' . $idTutor . '.' . $ext;
$rutaDestino = $rutaBase . $nuevoNombre;

// Si ya existe una foto anterior, borrarla
$datosTutor = $tutorModel->obtenerPorId($idTutor);
if (!empty($datosTutor['foto_perfil']) && file_exists($rutaBase . $datosTutor['foto_perfil'])) {
    unlink($rutaBase . $datosTutor['foto_perfil']);
}

// Mover el archivo
if (!move_uploaded_file($tmpName, $rutaDestino)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al guardar la imagen.']);
    exit;
}

// La ruta RELATIVA para guardar en la BD
$rutaRelativa = 'assets/img/tutores/' . $nuevoNombre;

echo json_encode([
    'ok' => true,
    'ruta' => $rutaRelativa,
    'mensaje' => 'Foto subida correctamente.'
]);