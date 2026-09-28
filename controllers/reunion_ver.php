<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ReunionMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.reuniones.ver_propias',
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
    header('Location: tutorados_listar.php');
    exit;
}

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloReunion = new ReunionMgModel($pdo);

$reunion = $modeloReunion->buscarPorIdParaTutor(
    $idReunion,
    $idUsuario
);

if (!$reunion) {
    header('Location: tutorados_listar.php');
    exit;
}

$asistenciaTutor = $reunion['asistencia_tutor'];
$asistenciaEstudiante = $reunion['asistencia_estudiante'];
$acuerdos = $reunion['acuerdos'] ?? '';
$observaciones = $reunion['observaciones'] ?? '';
$motivoCancelacion = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !usuarioTienePermiso(
            'mg.reuniones.editar_propias'
        )
    ) {
        header('Location: ../index.php?estado=acceso_denegado');
        exit;
    }

    $accion = $_POST['accion'] ?? '';

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página.';
    } elseif ($reunion['estado'] !== 'programada') {
        $error = 'Esta reunión ya no se encuentra programada.';
    } elseif ($accion === 'registrar_resultado') {
        $asistenciaTutor = trim(
            $_POST['asistencia_tutor'] ?? ''
        );

        $asistenciaEstudiante = trim(
            $_POST['asistencia_estudiante'] ?? ''
        );

        $acuerdos = trim(
            $_POST['acuerdos'] ?? ''
        );

        $observaciones = trim(
            $_POST['observaciones'] ?? ''
        );

        $asistenciasPermitidas = [
            'presente',
            'ausente',
            'justificada'
        ];

        if (
            $reunion['fecha_reunion'] > date('Y-m-d')
        ) {
            $error = 'No puedes registrar como realizada una reunión futura.';
        } elseif (
            !in_array(
                $asistenciaTutor,
                $asistenciasPermitidas,
                true
            )
            || !in_array(
                $asistenciaEstudiante,
                $asistenciasPermitidas,
                true
            )
        ) {
            $error = 'Selecciona estados de asistencia válidos.';
        } elseif (
            $acuerdos === ''
            || mb_strlen($acuerdos) > 3000
        ) {
            $error = 'Registra los acuerdos de la reunión sin superar los 3000 caracteres.';
        } elseif (
            mb_strlen($observaciones) > 3000
        ) {
            $error = 'Las observaciones no pueden superar los 3000 caracteres.';
        } else {
            try {
                $actualizada = $modeloReunion
                    ->registrarResultado(
                        $idReunion,
                        $idUsuario,
                        $asistenciaTutor,
                        $asistenciaEstudiante,
                        $acuerdos,
                        $observaciones !== ''
                            ? $observaciones
                            : null
                    );

                if (!$actualizada) {
                    throw new RuntimeException(
                        'La reunión ya fue modificada.'
                    );
                }

                header(
                    'Location: reunion_ver.php?id='
                    . $idReunion
                    . '&estado=realizada'
                );
                exit;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            } catch (PDOException $e) {
                $error = 'No fue posible registrar el resultado.';
            }
        }
    } elseif ($accion === 'cancelar') {
        $motivoCancelacion = trim(
            $_POST['motivo_cancelacion'] ?? ''
        );

        if (
            mb_strlen($motivoCancelacion) < 5
            || mb_strlen($motivoCancelacion) > 255
        ) {
            $error = 'El motivo debe contener entre 5 y 255 caracteres.';
        } else {
            try {
                $cancelada = $modeloReunion->cancelar(
                    $idReunion,
                    $idUsuario,
                    $motivoCancelacion
                );

                if (!$cancelada) {
                    throw new RuntimeException(
                        'La reunión ya fue modificada.'
                    );
                }

                header(
                    'Location: reunion_ver.php?id='
                    . $idReunion
                    . '&estado=cancelada'
                );
                exit;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            } catch (PDOException $e) {
                $error = 'No fue posible cancelar la reunión.';
            }
        }
    } else {
        $error = 'La operación solicitada no es válida.';
    }
}

$mensajes = [
    'realizada' => 'La asistencia y los resultados fueron registrados correctamente.',
    'cancelada' => 'La reunión fue cancelada correctamente.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tituloPagina = 'Detalle de reunión';
$rutaBase = '../';

require_once __DIR__
    . '/../views/reuniones/ver.php';