<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DefensaModel.php';
require_once __DIR__ . '/../models/DocumentoMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.documentos.ver',
    '../index.php'
);

$modeloDefensa = new DefensaModel($pdo);
$modeloDocumento = new DocumentoMgModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDefensa = filter_input(
        INPUT_POST,
        'id_defensa',
        FILTER_VALIDATE_INT
    );
} else {
    $idDefensa = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idDefensa) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$defensa = $modeloDefensa->buscarPorId(
    $idDefensa
);

if (
    !$defensa
    || !in_array(
        $defensa['estado'],
        ['programada', 'realizada'],
        true
    )
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

$puedeGenerar = usuarioTienePermiso(
    'mg.documentos.generar'
);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$puedeGenerar) {
        $error = 'No tienes permiso para generar documentos.';
    } elseif (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página.';
    } elseif (
        $modeloDocumento->defensaTieneCitaciones(
            $idDefensa
        )
    ) {
        $error = 'Las citaciones de esta defensa ya fueron generadas.';
    } else {
        try {
            $cantidad = $modeloDocumento
                ->generarCitaciones(
                    $idDefensa,
                    (int) $usuarioSesion['id_usuario']
                );

            if ($cantidad === 0) {
                $error = 'No fue posible generar las citaciones. Verifica los tribunales y las plantillas.';
            } else {
                header(
                    'Location: citaciones_defensa.php?id='
                    . $idDefensa
                    . '&estado=generadas'
                );
                exit;
            }
        } catch (PDOException $e) {
            $error = 'No fue posible generar las citaciones.';
        }
    }
}

$documentos = $modeloDocumento->listarPorDefensa(
    $idDefensa
);

$estado = $_GET['estado'] ?? '';

$mensaje = $estado === 'generadas'
    ? 'Las citaciones fueron generadas correctamente.'
    : '';

$tituloPagina = 'Citaciones de defensa';
$rutaBase = '../';

require_once __DIR__
    . '/../views/documentos/citaciones_defensa.php';