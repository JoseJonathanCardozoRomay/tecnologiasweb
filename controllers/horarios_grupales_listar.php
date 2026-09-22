<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$m = new HorarioGrupalModel($pdo);
$estado = (string)($_GET['estado'] ?? '');
$registros = $m->obtenerTodos(in_array($estado, ['activo','inactivo'], true) ? $estado : null);

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idTutor = $tutor ? (int)$tutor['id_tutor'] : 0;
    $registros = array_values(array_filter($registros, fn(array $h): bool => (int)$h['id_tutor'] === $idTutor));
} elseif (esEstudiante()) {
    // Los estudiantes solo visualizan horarios activos de la oferta universitaria.
    $registros = array_values(array_filter($registros, fn(array $h): bool => (string)$h['estado'] === 'activo'));
}

$tituloPagina = 'Horarios Grupales - Sistema de Tutorías';
require __DIR__.'/../views/horarios_grupales/listar.php';
