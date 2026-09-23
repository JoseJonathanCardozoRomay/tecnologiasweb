<?php
declare(strict_types=1);

/**
 * Devuelve los próximos días y bloques de una hora realmente disponibles
 * para una tutoría personal. La disponibilidad parte de los horarios
 * semanales del tutor y se descuentan las tutorías que ya ocupan ese horario.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaModel.php';

header('Content-Type: application/json; charset=utf-8');

$idTutor = validarId($_GET['id_tutor'] ?? null);
if (!$idTutor) {
    echo json_encode(['ok' => false, 'message' => 'Selecciona un tutor.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $pdo->prepare("SELECT u.nombre,u.apellido,u.estado FROM tutores t
    INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
    WHERE t.id_tutor=:id LIMIT 1");
$stmt->execute([':id' => $idTutor]);
$tutor = $stmt->fetch();

if (!$tutor || $tutor['estado'] !== 'activo') {
    echo json_encode(['ok' => false, 'message' => 'El tutor seleccionado no está activo.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$modelo = new TutoriaModel($pdo);
$hoy = new DateTimeImmutable('today');
$dias = [];

// Se muestran las próximas cuatro semanas para que el estudiante pueda
// escoger con claridad entre días y horas realmente libres.
for ($i = 0; $i < 28; $i++) {
    $fechaObj = $hoy->modify('+' . $i . ' day');
    $fecha = $fechaObj->format('Y-m-d');
    $diaNumero = (int)$fechaObj->format('N');

    // La tabla de disponibilidad se define de lunes a sábado.
    if ($diaNumero >= 7) {
        continue;
    }

    $slots = $modelo->obtenerSlotsDisponibles($idTutor, $fecha, 60, null, null);
    if (!$slots) {
        continue;
    }

    $stmtDisp = $pdo->prepare("SELECT hora_inicio,hora_fin FROM disponibilidad_tutor
        WHERE id_tutor=:t AND dia_semana=:d ORDER BY hora_inicio");
    $stmtDisp->execute([
        ':t' => $idTutor,
        ':d' => diaSemanaEspanol($fecha),
    ]);

    $dias[] = [
        'fecha' => $fecha,
        'dia' => $fechaObj->format('d'),
        'dia_nombre' => ['1'=>'Lunes','2'=>'Martes','3'=>'Miércoles','4'=>'Jueves','5'=>'Viernes','6'=>'Sábado'][(string)$diaNumero],
        'fecha_texto' => $fechaObj->format('d/m/Y'),
        'disponibilidad' => array_map(static fn(array $d): array => [
            'hora_inicio' => substr($d['hora_inicio'], 0, 5),
            'hora_fin' => substr($d['hora_fin'], 0, 5),
        ], $stmtDisp->fetchAll()),
        'slots' => $slots,
    ];
}

echo json_encode([
    'ok' => true,
    'tutor' => trim($tutor['nombre'].' '.$tutor['apellido']),
    'dias' => $dias,
], JSON_UNESCAPED_UNICODE);
