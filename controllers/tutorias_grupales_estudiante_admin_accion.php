<?php
declare(strict_types=1);

/** Gestiona manualmente participantes de una sesión desde administración. */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/NotificacionModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tutorias_grupales_listar.php');
exigirCsrf();
$idTutoria = validarId($_POST['id_tutoria_grupal'] ?? null);
$idEstudiante = validarId($_POST['id_estudiante'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaGrupalModel($pdo);

if (!$idTutoria || !$idEstudiante || !in_array($accion, ['aprobar','inscribir','retirar'], true)) {
    flash('danger', 'Datos de participación no válidos.');
    redirect('tutorias_grupales_listar.php');
}

$sesion = $m->obtenerPorId($idTutoria);
if (!$sesion) {
    flash('danger', 'La tutoría grupal no existe.');
    redirect('tutorias_grupales_listar.php');
}

try {
    if ($accion === 'aprobar') {
        $errores = $m->validarAprobacion($idTutoria, $idEstudiante);
        if ($errores) {
            flash('danger', implode(' ', $errores));
        } elseif ($m->aprobarSolicitud($idTutoria, $idEstudiante)) {
            $notif = new NotificacionModel($pdo);
            $notif->crear(
                $idEstudiante,
                'tutoria_grupal_aprobada',
                'Tu solicitud de tutoría grupal fue aprobada: '.($sesion['nombre_materia'] ?? 'Tutoría grupal').'.',
                '/controllers/tutorias_grupales_listar.php'
            );
            registrarAccion($pdo, 'APROBAR_INSCRIPCION', 'Tutorías grupales', 'Administración aprobó la solicitud del estudiante #'.$idEstudiante.' en la sesión #'.$idTutoria.'.');
            flash('success', 'Solicitud aprobada correctamente.');
        } else {
            flash('danger', 'La solicitud no está pendiente o ya fue gestionada.');
        }
    } elseif ($accion === 'inscribir') {
        $errores = $m->validarInscripcion($idTutoria, $idEstudiante);
        if ($errores) {
            flash('danger', implode(' ', $errores));
        } elseif ($m->inscribirEstudiante($idTutoria, $idEstudiante)) {
            registrarAccion($pdo, 'INSCRIBIR', 'Tutorías grupales', 'Administración inscribió directamente al estudiante #'.$idEstudiante.' en la sesión #'.$idTutoria.'.');
            flash('success', 'Estudiante inscrito y aprobado correctamente.');
        } else {
            flash('danger', 'No se pudo registrar la inscripción.');
        }
    } else {
        $estadoParticipacion = $m->estadoParticipacion($idTutoria, $idEstudiante);
        if ((string)$sesion['estado'] !== 'programada') {
            flash('danger', 'Solo puedes retirar participantes antes de iniciar la sesión.');
        } elseif (!in_array($estadoParticipacion, ['aprobada','inscrito'], true)) {
            flash('danger', 'Solo se puede retirar una inscripción aprobada desde administración.');
        } elseif ($m->retirarEstudiante($idTutoria, $idEstudiante)) {
            registrarAccion($pdo, 'RETIRAR', 'Tutorías grupales', 'Administración retiró al estudiante #'.$idEstudiante.' de la sesión #'.$idTutoria.'.');
            flash('success', 'Estudiante retirado correctamente.');
        } else {
            flash('danger', 'No se pudo retirar al estudiante.');
        }
    }
} catch (PDOException $e) {
    flash('danger', 'No se pudo actualizar la participación.');
}

redirect('tutorias_grupales_estudiantes.php?id='.$idTutoria);
