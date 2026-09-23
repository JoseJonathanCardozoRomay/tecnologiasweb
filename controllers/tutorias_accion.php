<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador', 'tutor', 'estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaModel.php';
require_once __DIR__.'/../models/TutorModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tutorias_personales_listar.php');
}

exigirCsrf();

$id = validarId($_POST['id_tutoria'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaModel($pdo);

$tutoria = $id ? $m->obtenerPorId($id) : false;
if (!$tutoria) {
    flash('danger', 'La tutoría no existe.');
    redirect('tutorias_personales_listar.php');
}

/*
 * Verificación de propiedad:
 * - El tutor solamente puede operar sus propias tutorías.
 * - El estudiante solamente puede operar sus propias tutorías.
 * - El administrador supervisa la confirmación final y la cancelación.
 */
$propioTutor = esTutor()
    ? (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario'])
    : null;

$propioEstudiante = esEstudiante()
    ? (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario'])
    : null;

if (
    esTutor()
    && (
        !$propioTutor
        || (int)$tutoria['id_tutor'] !== (int)$propioTutor['id_tutor']
    )
) {
    flash('danger', 'No tienes permiso sobre esta tutoría.');
    redirect('tutorias_personales_listar.php');
}

if (
    esEstudiante()
    && (
        !$propioEstudiante
        || (int)$tutoria['id_estudiante'] !== (int)$propioEstudiante['id_estudiante']
    )
) {
    flash('danger', 'No tienes permiso sobre esta tutoría.');
    redirect('tutorias_personales_listar.php');
}

/*
 * Flujo de tutoría personal:
 * 1) Estudiante solicita -> pendiente.
 * 2) Tutor acepta o rechaza. Al aceptar, sigue pendiente para el estudiante.
 * 3) Administración confirma la programación -> programada.
 * 4) Tutor puede marcarla realizada una vez programada.
 * La cancelación es exclusiva del administrador.
 */
if (!in_array($accion, ['aceptar','rechazar','confirmar_programacion','realizar','cancelar'], true)) {
    flash('danger', 'Acción no válida para una tutoría personal.');
    redirect('tutorias_personales_listar.php');
}

/*
 * Aceptar/rechazar: únicamente el tutor asignado puede responder la solicitud.
 */
if (in_array($accion, ['aceptar','rechazar'], true)) {
    if (!esTutor()) {
        flash('danger', 'Solo el tutor asignado puede responder esta solicitud.');
        redirect('tutorias_personales_listar.php');
    }

    $propioTutorId = $propioTutor ? (int)$propioTutor['id_tutor'] : 0;
    try {
        $ok = $accion === 'aceptar'
            ? $m->aceptarPorTutor((int)$id, $propioTutorId)
            : $m->rechazarPorTutor((int)$id, $propioTutorId);

        if ($ok) {
            $nuevoEstadoTutor = $accion === 'aceptar' ? 'aceptada' : 'rechazada';
            registrarAccion(
                $pdo,
                strtoupper($accion),
                'Tutorías personales',
                'El tutor #'.$propioTutorId.' '.($accion === 'aceptar' ? 'aceptó' : 'rechazó').' la solicitud personal #'.$id.'.'
            );
            flash(
                $accion === 'aceptar' ? 'success' : 'warning',
                $accion === 'aceptar'
                    ? 'Solicitud aceptada. Queda pendiente de confirmación administrativa.'
                    : 'Solicitud rechazada por el tutor.'
            );
        } else {
            flash('danger', 'La solicitud ya fue gestionada o no se encuentra en un estado compatible.');
        }
    } catch (PDOException $e) {
        flash('danger', 'No se pudo actualizar la respuesta del tutor.');
    }
    redirect('tutorias_personales_listar.php');
}

/*
 * Confirmación final: administración confirma la fecha/hora solamente cuando
 * el tutor ya aceptó. Esta operación cambia el estado visible a "programada".
 */
if ($accion === 'confirmar_programacion') {
    if (!esAdministrador()) {
        flash('danger', 'Solo administración puede confirmar la programación final.');
        redirect('tutorias_personales_listar.php');
    }

    if ((string)$tutoria['estado_tutor'] !== 'aceptada') {
        flash('danger', 'No se puede programar la tutoría hasta que el tutor la acepte.');
        redirect('tutorias_personales_listar.php');
    }

    $modalidad = (string)($_POST['modalidad_confirmacion'] ?? '');
    $lugarOEnlace = normalizarTexto((string)($_POST['lugar_o_enlace_admin'] ?? ''));

    if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
        flash('danger', 'Selecciona una modalidad válida para la programación.');
        redirect('tutorias_personales_listar.php');
    }

    if ($lugarOEnlace === '') {
        flash('danger', $modalidad === 'virtual'
            ? 'Debes indicar el enlace virtual de la tutoría.'
            : 'Debes indicar el aula donde se realizará la tutoría.');
        redirect('tutorias_personales_listar.php');
    }

    if ($modalidad === 'virtual' && !filter_var($lugarOEnlace, FILTER_VALIDATE_URL)) {
        flash('danger', 'El enlace virtual indicado no es una URL válida.');
        redirect('tutorias_personales_listar.php');
    }

    try {
        if ($m->confirmarPorAdmin((int)$id, (int)$_SESSION['id_usuario'], $modalidad, $lugarOEnlace)) {
            registrarAccion(
                $pdo,
                'PROGRAMAR',
                'Tutorías personales',
                'Administración confirmó y programó la tutoría personal #'.$id.' después de la aceptación del tutor. Modalidad: '.$modalidad.'.'
            );
            flash('success', 'La tutoría fue confirmada y ahora está programada.');
        } else {
            flash('danger', 'No se pudo confirmar la programación. Verifica que el tutor haya aceptado la solicitud.');
        }
    } catch (PDOException $e) {
        flash('danger', 'No se pudo registrar la confirmación administrativa.');
    }
    redirect('tutorias_personales_listar.php');
}

/*
 * Finalización: solo el tutor asignado puede marcar una sesión programada
 * como realizada.
 */
if ($accion === 'realizar') {
    if (!esTutor() || !$propioTutor || (int)$tutoria['id_tutor'] !== (int)$propioTutor['id_tutor']) {
        flash('danger', 'Solo el tutor asignado puede marcar la tutoría como realizada.');
        redirect('tutorias_personales_listar.php');
    }
    if ((string)$tutoria['estado'] !== 'programada') {
        flash('danger', 'La tutoría debe estar programada antes de marcarse como realizada.');
        redirect('tutorias_personales_listar.php');
    }
    try {
        if ($m->marcarRealizadaPorTutor((int)$id, (int)$propioTutor['id_tutor'])) {
            registrarAccion($pdo, 'REALIZAR', 'Tutorías personales', 'Tutoría personal #'.$id.' marcada como realizada.');
            flash('success', 'La tutoría fue marcada como realizada.');
        } else {
            flash('danger', 'No se pudo actualizar la tutoría.');
        }
    } catch (PDOException $e) {
        flash('danger', 'No se pudo actualizar el estado de la tutoría.');
    }
    redirect('tutorias_personales_listar.php');
}

/*
 * La cancelación exige un motivo y pertenece exclusivamente a administración.
 * Se conserva quién canceló, cuándo y por qué para mantener trazabilidad.
 */
if ($accion === 'cancelar') {
    if (!esAdministrador()) {
        flash('danger', 'La cancelación de tutorías personales corresponde exclusivamente a administración.');
        redirect('tutorias_personales_listar.php');
    }
    if (!in_array((string)$tutoria['estado'], ['pendiente','programada','en_proceso'], true)) {
        flash('danger', 'La tutoría ya no puede ser cancelada desde su estado actual.');
        redirect('tutorias_personales_listar.php');
    }
    $motivo = normalizarTexto((string)($_POST['motivo_cancelacion'] ?? ''));

    if (!textoValido($motivo, 5, 500)) {
        flash('danger', 'Debes indicar un motivo de cancelación de entre 5 y 500 caracteres.');
        redirect('tutorias_personales_listar.php');
    }

    try {
        $ok = $m->cancelar(
            (int)$id,
            $motivo,
            (int)$_SESSION['id_usuario']
        );

        if ($ok) {
            registrarAccion(
                $pdo,
                'CANCELAR',
                'Tutorías',
                'Tutoría #'.$id.' cancelada. Motivo: '.(function_exists('mb_substr') ? mb_substr($motivo, 0, 210) : substr($motivo, 0, 210))
            );
            flash('success', 'La tutoría fue cancelada y el motivo quedó registrado.');
        } else {
            flash('danger', 'No se pudo cancelar la tutoría.');
        }
    } catch (PDOException $e) {
        flash('danger', 'No se pudo registrar la cancelación. Comprueba que la base de datos tenga los campos de auditoría de cancelación.');
    }

    redirect('tutorias_personales_listar.php');
}

redirect('tutorias_personales_listar.php');
