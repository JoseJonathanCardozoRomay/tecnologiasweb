<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReunionMgModel.php';
require_once __DIR__ . '/../models/EvidenciaReunionModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.reuniones.editar_propias',
    '../index.php'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idReunion = filter_input(
        INPUT_POST,
        'id_reunion',
        FILTER_VALIDATE_INT
    );
} else {
    $idReunion = filter_input(
        INPUT_GET,
        'reunion',
        FILTER_VALIDATE_INT
    );
}

if (!$idReunion) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloReunion = new ReunionMgModel($pdo);
$modeloEvidencia = new EvidenciaReunionModel($pdo);

$reunion = $modeloReunion->buscarPorIdParaTutor(
    $idReunion,
    $idUsuario
);

if (!$reunion) {
    header('Location: tutorados_listar.php');
    exit;
}

$evidencias = $modeloEvidencia->listarPorReunion(
    $idReunion,
    $idUsuario
);

$tipo = 'documento';
$descripcion = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = trim(
        $_POST['tipo'] ?? ''
    );

    $descripcion = trim(
        $_POST['descripcion'] ?? ''
    );

    $tiposPermitidos = [
        'acta',
        'fotografia',
        'documento',
        'otro'
    ];

    $archivo = $_FILES['archivo'] ?? null;

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif ($reunion['estado'] !== 'realizada') {
        $error = 'Solo puedes adjuntar evidencias a una reunión realizada.';
    } elseif (count($evidencias) >= 10) {
        $error = 'La reunión alcanzó el máximo de 10 evidencias.';
    } elseif (
        !in_array(
            $tipo,
            $tiposPermitidos,
            true
        )
    ) {
        $error = 'Selecciona un tipo de evidencia válido.';
    } elseif (mb_strlen($descripcion) > 255) {
        $error = 'La descripción no puede superar los 255 caracteres.';
    } elseif (
        !$archivo
        || !isset(
            $archivo['error'],
            $archivo['tmp_name'],
            $archivo['name'],
            $archivo['size']
        )
    ) {
        $error = 'Selecciona un archivo válido.';
    } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
        $error = match ($archivo['error']) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE =>
                'El archivo supera el tamaño permitido.',
            UPLOAD_ERR_PARTIAL =>
                'El archivo no terminó de cargarse.',
            UPLOAD_ERR_NO_FILE =>
                'Selecciona un archivo.',
            default =>
                'Ocurrió un error durante la carga.'
        };
    } elseif (
        !is_uploaded_file(
            $archivo['tmp_name']
        )
    ) {
        $error = 'El archivo recibido no es válido.';
    } elseif (
        (int) $archivo['size'] < 1
        || (int) $archivo['size'] > 5 * 1024 * 1024
    ) {
        $error = 'El archivo debe pesar como máximo 5 MB.';
    } else {
        $detectorMime = new finfo(
            FILEINFO_MIME_TYPE
        );

        $mimeReal = $detectorMime->file(
            $archivo['tmp_name']
        );

        $formatosPermitidos = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];

        if (
            !is_string($mimeReal)
            || !isset(
                $formatosPermitidos[$mimeReal]
            )
        ) {
            $error = 'Solo se permiten archivos PDF, JPG o PNG.';
        } else {
            $extension = $formatosPermitidos[$mimeReal];

            $nombreOriginal = basename(
                str_replace(
                    '\\',
                    '/',
                    $archivo['name']
                )
            );

            $nombreOriginal = preg_replace(
                '/[^\p{L}\p{N}._ -]/u',
                '_',
                $nombreOriginal
            );

            $nombreOriginal = mb_substr(
                $nombreOriginal ?: 'evidencia.' . $extension,
                0,
                255
            );

            $nombreGuardado = bin2hex(
                random_bytes(16)
            ) . '.' . $extension;

            // La carpeta se encuentra fuera del directorio público.
            $directorioEvidencias = dirname(
                __DIR__,
                2
            ) . '/archivos_privados/evidencias_reuniones';

            if (
                !is_dir($directorioEvidencias)
                && !mkdir(
                    $directorioEvidencias,
                    0750,
                    true
                )
            ) {
                $error = 'No fue posible preparar el almacenamiento de evidencias.';
            } else {
                $rutaDestino = $directorioEvidencias
                    . '/'
                    . $nombreGuardado;

                $hashArchivo = hash_file(
                    'sha256',
                    $archivo['tmp_name']
                );

                if (
                    !is_string($hashArchivo)
                    || !move_uploaded_file(
                        $archivo['tmp_name'],
                        $rutaDestino
                    )
                ) {
                    $error = 'No fue posible guardar el archivo.';
                } else {
                    try {
                        $modeloEvidencia->crear(
                            $idReunion,
                            $tipo,
                            $nombreOriginal,
                            $nombreGuardado,
                            $mimeReal,
                            (int) $archivo['size'],
                            $hashArchivo,
                            $descripcion !== ''
                                ? $descripcion
                                : null,
                            $idUsuario
                        );

                        header(
                            'Location: evidencias_reunion.php?reunion='
                            . $idReunion
                            . '&estado=subida'
                        );
                        exit;
                    } catch (Throwable $e) {
                        // Evitamos dejar un archivo sin registro.
                        if (is_file($rutaDestino)) {
                            unlink($rutaDestino);
                        }

                        $error = 'No fue posible registrar la evidencia.';
                    }
                }
            }
        }
    }
}

$mensaje = (
    ($_GET['estado'] ?? '') === 'subida'
)
    ? 'La evidencia fue adjuntada correctamente.'
    : '';

$tituloPagina = 'Evidencias de reunión';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reuniones/evidencias.php';