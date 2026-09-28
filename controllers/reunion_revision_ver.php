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
    'mg.reuniones.validar',
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
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idReunion) {
    header(
        'Location: reuniones_revision_listar.php?estado=no_encontrada'
    );
    exit;
}

$modeloRevision = new RevisionSeguimientoModel($pdo);

$reunion = $modeloRevision->buscarReunion(
    $idReunion
);

if (!$reunion) {
    header(
        'Location: reuniones_revision_listar.php?estado=no_encontrada'
    );
    exit;
}

$evidencias = $modeloRevision
    ->listarEvidenciasReunion(
        $idReunion
    );

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
            ['validada', 'observada'],
            true
        )
    ) {
        $error = 'Selecciona una decisión válida.';
    } elseif (mb_strlen($observacion) > 255) {
        $error = 'La observación no puede superar los 255 caracteres.';
    } elseif (
        $decision === 'observada'
        && $observacion === ''
    ) {
        $error = 'Debes explicar qué debe corregir el tutor.';
    } elseif ($reunion['estado'] !== 'realizada') {
        $error = 'Solamente pueden validarse reuniones realizadas.';
    } else {
        try {
            $resultado = $modeloRevision
                ->validarReunion(
                    $idReunion,
                    $idUsuario,
                    $decision,
                    $observacion !== ''
                        ? $observacion
                        : null
                );

            if (!$resultado) {
                throw new RuntimeException(
                    'La reunión ya fue validada o modificada.'
                );
            }

            header(
                'Location: reuniones_revision_listar.php?estado='
                . $decision
            );
            exit;
        } catch (
            InvalidArgumentException
            | RuntimeException $e
        ) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'No fue posible registrar la validación.';
        }
    }
}

$tituloPagina = 'Validar reunión';
$rutaBase = '../';

require_once __DIR__
    . '/../views/revision/reunion_ver.php';