<?php
declare(strict_types=1);

/**
 * Módulo unificado de Proyectos de grado.
 *
 * Un único apartado reúne:
 * - los proyectos de grado registrados por los estudiantes;
 * - las solicitudes de tutoría personal asociadas a esos proyectos.
 *
 * La creación del proyecto pertenece al estudiante. La aceptación del tutor
 * y la confirmación administrativa de la tutoría se gestionan desde aquí.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';
require_once __DIR__.'/../models/TutoriaModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$pm = new ProyectoGradoModel($pdo);
$tm = new TutoriaModel($pdo);
$busqueda = trim((string)($_GET['q'] ?? ''));
$estadoProyecto = (string)($_GET['estado'] ?? '');
$seccion = (string)($_GET['seccion'] ?? 'proyectos');
$seccion = in_array($seccion, ['proyectos','solicitudes'], true) ? $seccion : 'proyectos';

$registros = $pm->obtenerTodos($busqueda !== '' ? $busqueda : null, $estadoProyecto !== '' ? $estadoProyecto : null);

// Un estudiante solo puede consultar sus propios proyectos.
if (esEstudiante()) {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idEstudiante = $estudiante ? (int)$estudiante['id_estudiante'] : 0;
    $registros = array_values(array_filter($registros, static fn(array $p): bool => (int)$p['id_estudiante'] === $idEstudiante));
}

// Recuperamos únicamente tutorías personales: una solicitud siempre está vinculada a un proyecto.
$solicitudes = array_values(array_filter(
    $tm->obtenerTodas(),
    static fn(array $r): bool => !empty($r['id_proyecto'])
));

// Cuando se filtra por estado de proyecto, el mismo filtro se refleja en las solicitudes.
$idsProyectosVisibles = array_map(static fn(array $p): int => (int)$p['id_proyecto'], $registros);

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idTutor = $tutor ? (int)$tutor['id_tutor'] : 0;
    $solicitudes = array_values(array_filter($solicitudes, static fn(array $r): bool => (int)$r['id_tutor'] === $idTutor));
} elseif (esEstudiante()) {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idEstudiante = $estudiante ? (int)$estudiante['id_estudiante'] : 0;
    $solicitudes = array_values(array_filter($solicitudes, static fn(array $r): bool => (int)$r['id_estudiante'] === $idEstudiante));
}

if ($estadoProyecto !== '') {
    $solicitudes = array_values(array_filter($solicitudes, static fn(array $r): bool => in_array((int)$r['id_proyecto'], $idsProyectosVisibles, true)));
}

// El buscador de proyectos también ayuda a localizar solicitudes por proyecto, estudiante o carrera.
if ($busqueda !== '') {
    $q = function_exists('mb_strtolower') ? mb_strtolower($busqueda) : strtolower($busqueda);
    $solicitudes = array_values(array_filter($solicitudes, static function(array $r) use ($q): bool {
        $texto = (function_exists('mb_strtolower') ? mb_strtolower : 'strtolower')(trim(
            ($r['proyecto_grado'] ?? '').' '.
            ($r['estudiante'] ?? '').' '.
            ($r['tutor'] ?? '').' '.
            ($r['nombre_carrera'] ?? '')
        ));
        return str_contains($texto, $q);
    }));
}

// Agrupamos las solicitudes por proyecto para mostrarlas dentro del mismo apartado.
$solicitudesPorProyecto = [];
foreach ($solicitudes as $solicitud) {
    $solicitudesPorProyecto[(int)$solicitud['id_proyecto']][] = $solicitud;
}

$tituloPagina = 'Proyectos de Grado - Sistema de Tutorías';
require __DIR__.'/../views/proyectos_grado/listar.php';
