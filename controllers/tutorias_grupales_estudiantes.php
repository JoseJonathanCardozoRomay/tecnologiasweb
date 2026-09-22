<?php
declare(strict_types=1);

/**
 * Consulta de participantes de una tutoría grupal.
 *
 * El administrador puede supervisar cualquier sesión y el tutor únicamente
 * las sesiones que le fueron asignadas por el horario institucional.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$id = validarId($_GET['id'] ?? null);
$m = new TutoriaGrupalModel($pdo);
$sesion = $id ? $m->obtenerPorId($id) : false;

if (!$sesion) {
    flash('danger', 'La tutoría grupal no existe.');
    redirect('tutorias_grupales_listar.php');
}

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    if (!$tutor || (int)$sesion['id_tutor'] !== (int)$tutor['id_tutor']) {
        flash('danger', 'No tienes permiso para consultar esta tutoría grupal.');
        redirect('tutorias_grupales_listar.php');
    }
}

$estudiantes = $m->estudiantes($id);
$disponibles = esAdministrador() && $sesion['estado'] === 'programada'
    ? $m->estudiantesDisponibles($id)
    : [];

$tituloPagina = 'Estudiantes de la tutoría grupal - Sistema de Tutorías';
require __DIR__.'/../views/tutorias_grupales/estudiantes.php';
