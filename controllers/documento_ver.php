<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DocumentoMgModel.php';
require_once __DIR__
    . '/../models/DocumentoAccesoModel.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirSesion();

$idDocumento = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idDocumento) {
    header('Location: ../index.php');
    exit;
}

$modeloDocumento = new DocumentoMgModel($pdo);
$modeloAcceso = new DocumentoAccesoModel($pdo);

$documento = $modeloDocumento->buscarPorId(
    $idDocumento
);

if (!$documento) {
    header('Location: ../index.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];
$rol = $usuarioSesion['rol'] ?? '';

$accesoGeneral = usuarioTienePermiso(
    'mg.documentos.ver'
);

$accesoPropio = false;

if (
    !$accesoGeneral
    && usuarioTienePermiso(
        'mg.documentos.ver_propios'
    )
) {
    if ($rol === 'estudiante') {
        $accesoPropio = $modeloAcceso
            ->perteneceAEstudiante(
                $idDocumento,
                $idUsuario
            );
    } elseif ($rol === 'tutor') {
        $accesoPropio = $modeloAcceso
            ->perteneceATutor(
                $idDocumento,
                $idUsuario
            );
    }
}

if (!$accesoGeneral && !$accesoPropio) {
    header(
        'Location: ../index.php?estado=acceso_denegado'
    );
    exit;
}

/*
 * La plantilla puede contener formato HTML, pero eliminamos
 * etiquetas y atributos peligrosos antes de mostrarla.
 */
$contenidoSeguro = strip_tags(
    $documento['contenido_snapshot'],
    '<h1><h2><h3><p><strong><em><ul><ol><li><br><hr>'
);

// Eliminamos todos los atributos de las etiquetas permitidas.
$contenidoSeguro = preg_replace(
    '/<([a-z][a-z0-9]*)\b[^>]*>/i',
    '<$1>',
    $contenidoSeguro
);

$tituloPagina = $documento['numero_correlativo'];
$rutaBase = '../';

require_once __DIR__
    . '/../views/documentos/ver.php';