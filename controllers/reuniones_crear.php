<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/AsignacionTutorModel.php';
require_once __DIR__ . '/../models/ReunionMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.reuniones.crear_propias',
    '../index.php'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idExpediente = filter_input(
        INPUT_POST,
        'id_expediente',
        FILTER_VALIDATE_INT
    );
} else {
    $idExpediente = filter_input(
        INPUT_GET,
        'expediente',
        FILTER_VALIDATE_INT
    );
}

if (!$idExpediente) {
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloAsignacion = new AsignacionTutorModel($pdo);
$modeloReunion = new ReunionMgModel($pdo);

$tutorado = $modeloAsignacion->buscarTutorado(
    $idExpediente,
    $idUsuario
);

if (!$tutorado) {
    header('Location: tutorados_listar.php');
    exit;
}

$fechaReunion = '';
$horaInicio = '';
$horaFin = '';
$modalidad = '';
$lugarEnlace = '';
$tema = '';
$acuerdos = '';
$observaciones = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fechaReunion = trim(
        $_POST['fecha_reunion'] ?? ''
    );

    $horaInicio = trim(
        $_POST['hora_inicio'] ?? ''
    );

    $horaFin = trim(
        $_POST['hora_fin'] ?? ''
    );

    $modalidad = trim(
        $_POST['modalidad'] ?? ''
    );

    $lugarEnlace = trim(
        $_POST['lugar_enlace'] ?? ''
    );

    $tema = trim(
        $_POST['tema'] ?? ''
    );

    $acuerdos = trim(
        $_POST['acuerdos'] ?? ''
    );

    $observaciones = trim(
        $_POST['observaciones'] ?? ''
    );

    $fechaValida = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $fechaReunion
    );

    $fechaCorrecta = (
        $fechaValida !== false
        && $fechaValida->format('Y-m-d') === $fechaReunion
    );

    $horarioValido = (
        preg_match(
            '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            $horaInicio
        )
        && preg_match(
            '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            $horaFin
        )
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif (!$fechaCorrecta) {
        $error = 'Selecciona una fecha válida.';
    } elseif (
        $fechaValida < new DateTimeImmutable('today')
    ) {
        $error = 'No puedes programar una reunión en una fecha pasada.';
    } elseif (!$horarioValido) {
        $error = 'Selecciona un horario válido.';
    } elseif ($horaFin <= $horaInicio) {
        $error = 'La hora de finalización debe ser posterior a la hora de inicio.';
    } elseif (
        !in_array(
            $modalidad,
            ['presencial', 'virtual'],
            true
        )
    ) {
        $error = 'Selecciona una modalidad válida.';
    } elseif (
        $lugarEnlace === ''
        || mb_strlen($lugarEnlace) > 255
    ) {
        $error = 'Ingresa un lugar o enlace válido.';
    } elseif (
        $modalidad === 'virtual'
        && !filter_var(
            $lugarEnlace,
            FILTER_VALIDATE_URL
        )
    ) {
        $error = 'La reunión virtual necesita un enlace válido.';
    } elseif (
        mb_strlen($tema) < 3
        || mb_strlen($tema) > 150
    ) {
        $error = 'El tema debe contener entre 3 y 150 caracteres.';
    } elseif (mb_strlen($acuerdos) > 3000) {
        $error = 'Los acuerdos no pueden superar los 3000 caracteres.';
    } elseif (mb_strlen($observaciones) > 3000) {
        $error = 'Las observaciones no pueden superar los 3000 caracteres.';
    } else {
        try {
            $modeloReunion->crear(
                $idExpediente,
                $idUsuario,
                $fechaReunion,
                $horaInicio,
                $horaFin,
                $modalidad,
                $lugarEnlace,
                $tema,
                $acuerdos !== ''
                    ? $acuerdos
                    : null,
                $observaciones !== ''
                    ? $observaciones
                    : null
            );

            header(
                'Location: reuniones_listar.php?expediente='
                . $idExpediente
                . '&estado=creada'
            );
            exit;
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'No fue posible programar la reunión.';
        }
    }
}

$tituloPagina = 'Programar reunión';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reuniones/crear.php';