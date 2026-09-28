<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RevisionSeguimientoModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.informes.editar',
    '../index.php'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idInforme = filter_input(
        INPUT_POST,
        'id_informe',
        FILTER_VALIDATE_INT
    );
} else {
    $idInforme = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idInforme) {
    header(
        'Location: informes_revision_listar.php?estado=no_encontrado'
    );
    exit;
}

$modeloRevision = new RevisionSeguimientoModel($pdo);

$informe = $modeloRevision->buscarInforme(
    $idInforme
);

if (!$informe) {
    header(
        'Location: informes_revision_listar.php?estado=no_encontrado'
    );
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$observacion = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision = $_POST['decision'] ?? '';

    $observacion = trim(
        $_POST['observacion'] ?? ''
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif (
        !in_array(
            $decision,
            ['aprobado', 'observado'],
            true
        )
    ) {
        $error = 'Selecciona una decisión válida.';
    } elseif (mb_strlen($observacion) > 500) {
        $error = 'La observación no puede superar los 500 caracteres.';
    } elseif (
        $decision === 'observado'
        && $observacion === ''
    ) {
        $error = 'Debes explicar qué debe corregir el tutor.';
    } else {
        try {
            $modeloRevision->revisarInforme(
                $idInforme,
                $idUsuario,
                $decision,
                $observacion !== ''
                    ? $observacion
                    : null
            );

            header(
                'Location: informes_revision_listar.php?estado='
                . $decision
            );
            exit;
        } catch (
            InvalidArgumentException
            | RuntimeException $e
        ) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'No fue posible registrar la revisión.';
        }
    }
}

$tituloPagina = 'Revisar informe';
$rutaBase = '../';

require_once __DIR__
    . '/../views/revision/informe_ver.php';