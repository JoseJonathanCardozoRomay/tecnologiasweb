<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RevisionSeguimientoModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.reuniones.validar',
    '../index.php'
);

$idEvidencia = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idEvidencia) {
    http_response_code(404);
    exit('Evidencia no encontrada.');
}

$modeloRevision = new RevisionSeguimientoModel($pdo);

$evidencia = $modeloRevision->buscarEvidencia(
    $idEvidencia
);

if (!$evidencia) {
    http_response_code(404);
    exit('Evidencia no encontrada.');
}

// Solo aceptamos el nombre aleatorio generado por el sistema.
$nombreGuardado = basename(
    $evidencia['ruta_archivo']
);

if (
    $nombreGuardado !== $evidencia['ruta_archivo']
    || !preg_match(
        '/^[a-f0-9]{32}\.(pdf|jpg|png)$/',
        $nombreGuardado
    )
) {
    http_response_code(404);
    exit('Archivo no disponible.');
}

$directorioEvidencias = dirname(
    __DIR__,
    2
) . '/archivos_privados/evidencias_reuniones';

$rutaArchivo = $directorioEvidencias
    . '/'
    . $nombreGuardado;

$directorioReal = realpath(
    $directorioEvidencias
);

$rutaReal = realpath(
    $rutaArchivo
);

if (
    $directorioReal === false
    || $rutaReal === false
    || !is_file($rutaReal)
    || !str_starts_with(
        $rutaReal,
        $directorioReal . DIRECTORY_SEPARATOR
    )
) {
    http_response_code(404);
    exit('Archivo no disponible.');
}

// Comprobamos que el archivo conserve su contenido original.
$hashActual = hash_file(
    'sha256',
    $rutaReal
);

if (
    !is_string($hashActual)
    || !is_string($evidencia['hash_archivo'])
    || !hash_equals(
        $evidencia['hash_archivo'],
        $hashActual
    )
) {
    http_response_code(409);
    exit('La integridad del archivo no pudo verificarse.');
}

$tiposPermitidos = [
    'application/pdf',
    'image/jpeg',
    'image/png'
];

$tipoMime = in_array(
    $evidencia['tipo_mime'],
    $tiposPermitidos,
    true
)
    ? $evidencia['tipo_mime']
    : 'application/octet-stream';

$nombreOriginal = str_replace(
    ["\r", "\n", '"'],
    '',
    $evidencia['nombre_original']
);

$nombreAlternativo = preg_replace(
    '/[^a-zA-Z0-9._-]/',
    '_',
    $nombreOriginal
);

if (
    !is_string($nombreAlternativo)
    || $nombreAlternativo === ''
) {
    $nombreAlternativo = 'evidencia';
}

session_write_close();

header('Content-Type: ' . $tipoMime);
header(
    'Content-Length: '
    . (string) filesize($rutaReal)
);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
header('Pragma: no-cache');

header(
    'Content-Disposition: attachment; filename="'
    . $nombreAlternativo
    . '"; filename*=UTF-8\'\''
    . rawurlencode($nombreOriginal)
);

readfile($rutaReal);
exit;