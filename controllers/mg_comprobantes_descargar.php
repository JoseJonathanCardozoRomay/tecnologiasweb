<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['estudiante', 'administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ComprobanteModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../includes/archivos.php';

$id_comprobante = (int) ($_GET['id'] ?? 0);
if ($id_comprobante <= 0) {
    responder_error_archivo(400, 'Solicitud inválida: falta el identificador del comprobante.');
}

$comprobante = (new ComprobanteModel($pdo))->obtenerPorId($id_comprobante);
if (!$comprobante) {
    responder_error_archivo(404, 'El comprobante solicitado no existe.');
}

$rol = $_SESSION['rol'] ?? '';
if ($rol === 'estudiante') {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
    if (!$estudiante || (int) $comprobante['id_estudiante'] !== (int) $estudiante['id_estudiante']) {
        http_response_code(403);
        if (es_peticion_json()) {
            responder_error_archivo(403, 'No tenés permiso para descargar este comprobante.');
        }
        require __DIR__ . '/../views/errores/403.php';
        exit;
    }
}

$ruta = trim((string) ($comprobante['ruta_archivo'] ?? ''));
if ($ruta === '') {
    responder_error_archivo(404, 'Este comprobante no tiene archivo adjunto.');
}

$verificacion = verify_stored_file($ruta, 'uploads/comprobantes');

$extension = strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION));
$nombre = 'comprobante_' . $id_comprobante . '_' . date('Ymd') . ($extension !== '' ? '.' . $extension : '');

$inline = ($_GET['ver'] ?? 'inline') !== 'descargar';

servir_archivo_almacenado($verificacion, $nombre, $inline);
