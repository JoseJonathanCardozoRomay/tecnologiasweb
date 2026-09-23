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
$buscar = trim((string)($_GET['buscar'] ?? ''));
$registros = $m->obtenerTodos(in_array($estado, ['activo','inactivo'], true) ? $estado : null);

// El buscador se aplica sobre la materia sin alterar el resto de filtros ni permisos.
if ($buscar !== '') {
    $termino = function_exists('mb_strtolower') ? mb_strtolower($buscar, 'UTF-8') : strtolower($buscar);
    $registros = array_values(array_filter($registros, function (array $h) use ($termino): bool {
        $materia = (string)($h['nombre_materia'] ?? '');
        $materiaNormalizada = function_exists('mb_strtolower') ? mb_strtolower($materia, 'UTF-8') : strtolower($materia);
        return str_contains($materiaNormalizada, $termino);
    }));
}

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idTutor = $tutor ? (int)$tutor['id_tutor'] : 0;
    $registros = array_values(array_filter($registros, fn(array $h): bool => (int)$h['id_tutor'] === $idTutor));
} elseif (esEstudiante()) {
    // Los estudiantes solo visualizan horarios activos de la oferta universitaria.
    $registros = array_values(array_filter($registros, fn(array $h): bool => (string)$h['estado'] === 'activo'));
}

// Agrupación fija para que la oferta quede visualmente separada por turno.
$turnos = [
    'manana' => ['titulo' => 'Mañana', 'horario' => '07:00 – 10:00', 'clase' => 'primary', 'orden' => 1],
    'tarde' => ['titulo' => 'Tarde', 'horario' => '15:00 – 18:00', 'clase' => 'warning', 'orden' => 2],
    'noche' => ['titulo' => 'Noche', 'horario' => '19:00 – 22:00', 'clase' => 'dark', 'orden' => 3],
];

$registrosPorTurno = array_fill_keys(array_keys($turnos), []);
foreach ($registros as $registro) {
    $turno = (string)($registro['turno'] ?? '');
    if (isset($registrosPorTurno[$turno])) {
        $registrosPorTurno[$turno][] = $registro;
    }
}

foreach ($registrosPorTurno as &$items) {
    usort($items, static function (array $a, array $b): int {
        $diaA = (string)($a['dia_semana'] ?? '');
        $diaB = (string)($b['dia_semana'] ?? '');
        $dias = ['Lunes' => 1, 'Martes' => 2, 'Miercoles' => 3, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5];
        return ($dias[$diaA] ?? 99) <=> ($dias[$diaB] ?? 99);
    });
}
unset($items);

$tituloPagina = 'Horarios Grupales - Sistema de Tutorías';
require __DIR__.'/../views/horarios_grupales/listar.php';
