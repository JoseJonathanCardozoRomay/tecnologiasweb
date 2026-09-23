<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$idMateria = validarId($_GET['id_materia'] ?? null);
$idTutor = validarId($_GET['id_tutor'] ?? null);
$fecha = trim((string)($_GET['fecha'] ?? ''));

if (!$idMateria || !$idTutor || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
    echo json_encode(['ok' => false, 'message' => 'Selecciona una materia, un tutor y una fecha válidos.']);
    exit;
}

// Las tutorías grupales solo se pueden generar de lunes a viernes.
$numeroDia = (int)date('N', strtotime($fecha));
$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes'];
if (!isset($dias[$numeroDia])) {
    echo json_encode(['ok' => false, 'message' => 'La fecha seleccionada debe corresponder a un día de lunes a viernes.']);
    exit;
}

$dia = $dias[$numeroDia];

// Devuelve únicamente horarios institucionales activos del tutor para la materia
// elegida y el día seleccionado. Además se excluyen los horarios que ya tienen
// una sesión creada para esa fecha, evitando duplicar una tutoría.
$stmt = $pdo->prepare(
    "SELECT h.id_horario, h.dia_semana, h.turno,
            TIME_FORMAT(h.hora_inicio, '%H:%i') AS hora_inicio,
            TIME_FORMAT(h.hora_fin, '%H:%i') AS hora_fin
     FROM horarios_tutoria_grupal h
     INNER JOIN materias m ON m.id_materia = h.id_materia
     INNER JOIN carreras c ON c.id_carrera = m.id_carrera
     INNER JOIN tutores t ON t.id_tutor = h.id_tutor
     INNER JOIN usuarios u ON u.id_usuario = t.id_usuario
     LEFT JOIN tutorias_grupales tg
            ON tg.id_horario = h.id_horario
           AND tg.fecha = :fecha
     WHERE h.id_materia = :materia
       AND h.id_tutor = :tutor
       AND h.dia_semana = :dia
       AND h.estado = 'activo'
       AND m.estado = 'activo'
       AND c.estado = 'activo'
       AND u.estado = 'activo'
       AND tg.id_tutoria_grupal IS NULL
     ORDER BY FIELD(h.turno, 'manana', 'tarde', 'noche'), h.hora_inicio"
);
$stmt->execute([
    ':fecha' => $fecha,
    ':materia' => $idMateria,
    ':tutor' => $idTutor,
    ':dia' => $dia,
]);

$horarios = $stmt->fetchAll();

if (!$horarios) {
    echo json_encode([
        'ok' => true,
        'dia' => $dia,
        'slots' => [],
        'message' => 'No hay un horario institucional libre para ese docente, materia y día.',
    ]);
    exit;
}

echo json_encode(['ok' => true, 'dia' => $dia, 'slots' => $horarios]);
