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
    redirect('tutorias_listar.php');
}

exigirCsrf();

$id = validarId($_POST['id_tutoria'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaModel($pdo);

$tutoria = $id ? $m->obtenerPorId($id) : false;
if (!$tutoria) {
    flash('danger', 'La tutoría no existe.');
    redirect('tutorias_listar.php');
}

/*
 * Verificación de propiedad:
 * - El tutor solamente puede operar sus propias tutorías.
 * - El estudiante solamente puede operar sus propias tutorías.
 * - El administrador supervisa, pero NO confirma, rechaza ni marca
 *   tutorías como realizadas.
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
    redirect('tutorias_listar.php');
}

if (
    esEstudiante()
    && (
        !$propioEstudiante
        || (int)$tutoria['id_estudiante'] !== (int)$propioEstudiante['id_estudiante']
    )
) {
    flash('danger', 'No tienes permiso sobre esta tutoría.');
    redirect('tutorias_listar.php');
}

/*
 * Permisos por rol:
 * Administrador: únicamente cancelar.
 * Tutor: confirmar, rechazar, realizar o cancelar sus tutorías.
 * Estudiante: únicamente cancelar sus propias tutorías.
 */
$permitidasPorRol = [
    'administrador' => ['cancelar'],
    'tutor' => ['confirmar', 'rechazar', 'realizar', 'cancelar'],
    'estudiante' => ['cancelar'],
];

$rol = (string)($_SESSION['rol'] ?? '');
if (!in_array($accion, $permitidasPorRol[$rol] ?? [], true)) {
    flash(
        'danger',
        'Esta acción no está permitida para tu rol. El administrador solo puede supervisar y cancelar tutorías.'
    );
    redirect('tutorias_listar.php');
}

$mapaEstados = [
    'confirmar' => 'confirmada',
    'rechazar' => 'rechazada',
    'realizar' => 'realizada',
    'cancelar' => 'cancelada',
];

$nuevoEstado = $mapaEstados[$accion] ?? null;

if (
    $nuevoEstado === null
    || !$m->transicionPermitida((string)$tutoria['estado'], $nuevoEstado)
) {
    flash('danger', 'Ese cambio de estado no está permitido.');
    redirect('tutorias_listar.php');
}

/*
 * La cancelación exige un motivo. Se guarda junto con el usuario y
 * la fecha para conservar trazabilidad.
 */
if ($accion === 'cancelar') {
    $motivo = normalizarTexto((string)($_POST['motivo_cancelacion'] ?? ''));

    if (!textoValido($motivo, 5, 500)) {
        flash('danger', 'Debes indicar un motivo de cancelación de entre 5 y 500 caracteres.');
        redirect('tutorias_listar.php');
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

    redirect('tutorias_listar.php');
}

/*
 * Confirmar, rechazar y realizar quedan exclusivamente en manos
 * del tutor propietario.
 */
if (!esTutor()) {
    flash('danger', 'Solo el tutor asignado puede realizar esta acción.');
    redirect('tutorias_listar.php');
}

try {
    if ($m->cambiarEstado((int)$id, $nuevoEstado)) {
        registrarAccion(
            $pdo,
            strtoupper($nuevoEstado),
            'Tutorías',
            'Tutoría #'.$id.' cambió de '.$tutoria['estado'].' a '.$nuevoEstado.'.'
        );

        flash(
            'success',
            'La tutoría ahora figura como '.estadoEtiqueta($nuevoEstado).'.'
        );
    } else {
        flash('danger', 'No se pudo actualizar el estado.');
    }
} catch (PDOException $e) {
    flash('danger', 'No se pudo actualizar el estado de la tutoría.');
}

redirect('tutorias_listar.php');
