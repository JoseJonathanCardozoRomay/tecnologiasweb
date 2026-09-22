<?php
declare(strict_types=1);

/**
 * Gestiona inscripción y retiro de estudiantes en tutorías grupales.
 *
 * El estudiante nunca puede modificar el horario institucional: solamente
 * puede solicitar su inscripción o retiro de una sesión disponible.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tutorias_grupales_listar.php');
}

exigirCsrf();
$idSesion = validarId($_POST['id_tutoria_grupal'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaGrupalModel($pdo);
$em = new EstudianteModel($pdo);
$estudiante = $em->obtenerPorUsuario((int)$_SESSION['id_usuario']);

if (!$idSesion || !$estudiante) {
    flash('danger', 'No se pudo identificar la sesión o tu perfil de estudiante.');
    redirect('tutorias_grupales_listar.php');
}

$idEstudiante = (int)$estudiante['id_estudiante'];

try {
    if ($accion === 'inscribirse') {
        $errores = $m->validarInscripcion($idSesion, $idEstudiante);
        if ($errores) {
            flash('danger', implode(' ', $errores));
        } elseif ($m->inscribirEstudiante($idSesion, $idEstudiante)) {
            registrarAccion(
                $pdo,
                'INSCRIBIR',
                'Tutorías grupales',
                'El estudiante #'.$idEstudiante.' se inscribió en la sesión #'.$idSesion.'.'
            );
            flash('success', 'Te inscribiste correctamente en la tutoría grupal.');
        } else {
            flash('danger', 'No se pudo registrar la inscripción.');
        }
    } elseif ($accion === 'retirarse') {
        $sesion = $m->obtenerPorId($idSesion);
        if (!$sesion || !in_array((string)$sesion['estado'], ['programada'], true)) {
            flash('danger', 'Solo puedes retirarte de una sesión que todavía está programada.');
        } elseif ($m->retirarEstudiante($idSesion, $idEstudiante)) {
            registrarAccion(
                $pdo,
                'RETIRAR',
                'Tutorías grupales',
                'El estudiante #'.$idEstudiante.' se retiró de la sesión #'.$idSesion.'.'
            );
            flash('success', 'Te retiraste de la tutoría grupal.');
        } else {
            flash('danger', 'No se pudo registrar el retiro.');
        }
    } else {
        flash('danger', 'Acción no válida.');
    }
} catch (PDOException $e) {
    flash('danger', 'No se pudo actualizar tu inscripción.');
}

redirect('tutorias_grupales_listar.php');
