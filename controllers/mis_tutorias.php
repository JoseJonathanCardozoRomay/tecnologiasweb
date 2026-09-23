<?php
declare(strict_types=1);

/**
 * Obtiene la agenda del usuario autenticado separando las dos modalidades
 * de tutoría para que su funcionamiento y estado sean fáciles de distinguir.
 *
 * - Tutorías de materias: sesiones grupales vinculadas a horarios universitarios.
 * - Tutorías de proyectos de grado: acompañamientos personales de un estudiante.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DashboardModel.php';
require_once __DIR__.'/../models/TutorModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$dashboard = new DashboardModel($pdo);
$personales = [];
$grupales = [];
$rol = (string)$_SESSION['rol'];

if ($rol === 'tutor') {
    $perfil = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    if ($perfil) {
        $personales = $dashboard->proximasTutoriasTutor((int)$perfil['id_tutor']);
        $grupales = $dashboard->proximasGrupalesTutor((int)$perfil['id_tutor']);
    }
} else {
    $perfil = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    if ($perfil) {
        $personales = $dashboard->proximasTutoriasEstudiante((int)$perfil['id_estudiante']);
        $grupales = $dashboard->proximasGrupalesEstudiante((int)$perfil['id_estudiante']);
    }
}

/**
 * Ordena cada modalidad de forma independiente y limita la cantidad para
 * evitar que una sola sección desplace completamente a la otra.
 */
$ordenarAgenda = static function (array $agenda): array {
    usort($agenda, static function (array $a, array $b): int {
        $ta = strtotime(($a['fecha'] ?? '').' '.($a['hora_inicio'] ?? '00:00:00')) ?: PHP_INT_MAX;
        $tb = strtotime(($b['fecha'] ?? '').' '.($b['hora_inicio'] ?? '00:00:00')) ?: PHP_INT_MAX;
        return $ta <=> $tb;
    });
    return array_slice($agenda, 0, 20);
};

$personales = $ordenarAgenda($personales);
$grupales = $ordenarAgenda($grupales);

$tituloPagina = 'Mis Tutorías - Sistema de Tutorías';
require __DIR__.'/../views/tutorias/mis_tutorias.php';
