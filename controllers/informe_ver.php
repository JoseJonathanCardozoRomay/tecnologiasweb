<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/InformeAvanceModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.informes.ver_propios',
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
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloInforme = new InformeAvanceModel($pdo);

$informe = $modeloInforme->buscarPorIdParaTutor(
    $idInforme,
    $idUsuario
);

if (!$informe) {
    header('Location: tutorados_listar.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !usuarioTienePermiso(
            'mg.informes.editar_propios'
        )
    ) {
        header(
            'Location: ../index.php?estado=acceso_denegado'
        );
        exit;
    }

    $accion = $_POST['accion'] ?? '';

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif ($accion !== 'presentar') {
        $error = 'La operación solicitada no es válida.';
    } elseif (
        !in_array(
            $informe['estado'],
            ['borrador', 'observado'],
            true
        )
    ) {
        $error = 'El informe ya no puede presentarse.';
    } else {
        try {
            $presentado = $modeloInforme->presentar(
                $idInforme,
                $idUsuario
            );

            if (!$presentado) {
                throw new RuntimeException(
                    'El informe ya fue modificado.'
                );
            }

            header(
                'Location: informe_ver.php?id='
                . $idInforme
                . '&estado=presentado'
            );
            exit;
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'No fue posible presentar el informe.';
        }
    }
}

$mensaje = (
    ($_GET['estado'] ?? '') === 'presentado'
)
    ? 'El informe fue presentado a coordinación.'
    : '';

$tituloPagina = 'Detalle del informe';
$rutaBase = '../';

require_once __DIR__
    . '/../views/informes/ver.php';