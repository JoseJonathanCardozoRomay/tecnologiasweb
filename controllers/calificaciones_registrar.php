<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/CalificacionModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.calificaciones.editar',
    '../index.php'
);

$modeloCalificacion = new CalificacionModel($pdo);
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
        'id_defensa',
        FILTER_VALIDATE_INT
    );
}

if (!$idDefensa) {
    header(
        'Location: defensas_listar.php?estado=no_encontrado'
    );
    exit;
}

$datosDefensa = $modeloCalificacion->buscarPorDefensa(
    $idDefensa
);

if (
    !$datosDefensa
    || $datosDefensa['estado_defensa'] !== 'realizada'
) {
    header(
        'Location: defensas_listar.php?estado=error'
    );
    exit;
}

$limites = $modeloCalificacion->obtenerLimites();

$nota = $datosDefensa['nota'] ?? '';
$observaciones = $datosDefensa['observaciones'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notaRecibida = trim(
        $_POST['nota'] ?? ''
    );

    $observaciones = trim(
        $_POST['observaciones'] ?? ''
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página.';
    } elseif (
        $notaRecibida === ''
        || !is_numeric($notaRecibida)
    ) {
        $error = 'Ingresa una calificación válida.';
    } else {
        $nota = (float) $notaRecibida;

        if (
            $nota < $limites['minimo']
            || $nota > $limites['maximo']
        ) {
            $error = 'La calificación debe estar entre '
                . $limites['minimo']
                . ' y '
                . $limites['maximo']
                . '.';
        } elseif (
            mb_strlen($observaciones) > 500
        ) {
            $error = 'Las observaciones no pueden superar los 500 caracteres.';
        } else {
            try {
                $modeloCalificacion->guardar(
                    $idDefensa,
                    $nota,
                    $observaciones !== ''
                        ? $observaciones
                        : null,
                    (int) $usuarioSesion['id_usuario']
                );

                header(
                    'Location: calificaciones_registrar.php?id_defensa='
                    . $idDefensa
                    . '&estado=guardada'
                );
                exit;
            } catch (PDOException $e) {
                $error = 'No fue posible guardar la calificación.';
            }
        }
    }
}

$datosDefensa = $modeloCalificacion->buscarPorDefensa(
    $idDefensa
);

$estado = $_GET['estado'] ?? '';

$mensajes = [
    'guardada' => 'La calificación fue guardada. Debes publicarla para que el estudiante pueda verla.',
    'publicada' => 'La calificación ya está visible para el estudiante.',
    'ocultada' => 'La calificación fue ocultada al estudiante.',
    'error' => 'No fue posible completar la operación.'
];

$mensaje = $mensajes[$estado] ?? '';

$tituloPagina = 'Registrar calificación';
$rutaBase = '../';

require_once __DIR__
    . '/../views/calificaciones/registrar.php';