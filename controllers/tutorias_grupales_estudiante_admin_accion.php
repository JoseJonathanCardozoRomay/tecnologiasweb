<?php
declare(strict_types=1);

/** Gestiona manualmente participantes de una sesión desde administración. */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tutorias_grupales_listar.php');
exigirCsrf();
$idTutoria = validarId($_POST['id_tutoria_grupal'] ?? null);
$idEstudiante = validarId($_POST['id_estudiante'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaGrupalModel($pdo);

if (!$idTutoria || !$idEstudiante || !in_array($accion, ['inscribir','retirar'], true)) {
    flash('danger', 'Datos de participación no válidos.');
    redirect('tutorias_grupales_listar.php');
}

$sesion = $m->obtenerPorId($idTutoria);
if (!$sesion) {
    flash('danger', 'La tutoría grupal no existe.');
    redirect('tutorias_grupales_listar.php');
}

try {
    if ($accion === 'inscribir') {
        $errores = $m->validarInscripcion($idTutoria, $idEstudiante);
        if ($errores) {
            flash('danger', implode(' ', $errores));
        } elseif ($m->inscribirEstudiante($idTutoria, $idEstudiante)) {
            registrarAccion($pdo, 'INSCRIBIR', 'Tutorías grupales', 'Administración inscribió al estudiante #'.$idEstudiante.' en la sesión #'.$idTutoria.'.');
            flash('success', 'Estudiante inscrito correctamente.');
        } else {
            flash('danger', 'No se pudo registrar la inscripción.');
        }
    } else {
        if ((string)$sesion['estado'] !== 'programada') {
            flash('danger', 'Solo puedes retirar participantes antes de iniciar la sesión.');
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
