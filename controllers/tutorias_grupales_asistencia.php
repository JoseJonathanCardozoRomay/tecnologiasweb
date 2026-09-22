<?php
declare(strict_types=1);

/** Registra la asistencia de un estudiante por parte de su tutor asignado. */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/TutorModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tutorias_grupales_listar.php');
exigirCsrf();
$idTutoria = validarId($_POST['id_tutoria_grupal'] ?? null);
$idEstudiante = validarId($_POST['id_estudiante'] ?? null);
$estado = (string)($_POST['estado'] ?? '');
$m = new TutoriaGrupalModel($pdo);

if (!$idTutoria || !$idEstudiante || !in_array($estado, ['asistio','no_asistio'], true)) {
    flash('danger', 'Datos de asistencia no válidos.');
    redirect('tutorias_grupales_listar.php');
}

$sesion = $m->obtenerPorId($idTutoria);
$tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
if (!$sesion || !$tutor || (int)$sesion['id_tutor'] !== (int)$tutor['id_tutor']) {
    flash('danger', 'No tienes permiso para registrar asistencia en esta sesión.');
    redirect('tutorias_grupales_listar.php');
}

if (!in_array((string)$sesion['estado'], ['en_curso','realizada'], true)) {
    flash('danger', 'La asistencia solo puede registrarse cuando la sesión está en curso o finalizada.');
    redirect('tutorias_grupales_estudiantes.php?id='.$idTutoria);
}

try {
    if ($m->cambiarEstadoEstudiante($idTutoria, $idEstudiante, $estado)) {
        registrarAccion($pdo, 'ASISTENCIA', 'Tutorías grupales', 'El tutor registró '.$estado.' para el estudiante #'.$idEstudiante.' en la sesión #'.$idTutoria.'.');
        flash('success', 'Asistencia actualizada.');
    } else {
        flash('danger', 'No se pudo actualizar la asistencia.');
    }
} catch (PDOException $e) {
    flash('danger', 'No se pudo registrar la asistencia.');
}
redirect('tutorias_grupales_estudiantes.php?id='.$idTutoria);
