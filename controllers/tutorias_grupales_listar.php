<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/TutorModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$m = new TutoriaGrupalModel($pdo);
$estado = (string)($_GET['estado'] ?? '');
$registros = $m->obtenerTodas(in_array($estado, ['programada','en_curso','realizada','cancelada'], true) ? $estado : null);

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idTutor = $tutor ? (int)$tutor['id_tutor'] : 0;
    $registros = array_values(array_filter($registros, fn(array $r): bool => (int)$r['id_tutor'] === $idTutor));
} elseif (esEstudiante()) {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idEstudiante = $estudiante ? (int)$estudiante['id_estudiante'] : 0;
    // El estudiante puede visualizar la oferta activa. La vista indicará si está inscrito.
    $registros = array_values(array_filter($registros, fn(array $r): bool => (string)$r['estado_horario'] === 'activo'));
    $participaciones = [];
    if ($idEstudiante > 0) {
        foreach ($registros as $r) {
            $participaciones[(int)$r['id_tutoria_grupal']] = $m->estadoParticipacion((int)$r['id_tutoria_grupal'], $idEstudiante);
        }
    }
} else {
    $participaciones = [];
}

$tituloPagina = 'Tutorías Grupales - Sistema de Tutorías';
require __DIR__.'/../views/tutorias_grupales/listar.php';
