<?php

const TUTORIA_DURACION_MIN = 30;
const TUTORIA_DURACION_MAX = 120;
const TUTORIA_ANTICIPACION_HORAS = 2;
const TUTORIA_MAX_ACTIVAS = 3;

// pendiente de definir en la entrevista de requerimientos; aún no se aplica
const TUTORIA_CUPO_MAXIMO_GRUPO = 30;

function validarSolicitudTutoria(PDO $pdo, array $datos, array $estudiante): array
{
    $errores = [];
    $idTutor = filter_var($datos['id_tutor'] ?? null, FILTER_VALIDATE_INT);
    $idMateria = filter_var($datos['id_materia'] ?? null, FILTER_VALIDATE_INT);
    $fecha = $datos['fecha'] ?? '';
    $inicio = $datos['hora_inicio'] ?? '';
    $fin = $datos['hora_fin'] ?? '';

    $stmt = $pdo->prepare('SELECT t.id_tutor FROM tutores t INNER JOIN usuarios u ON u.id_usuario = t.id_usuario WHERE t.id_tutor = :id AND u.estado = \'activo\'');
    $stmt->execute([':id' => $idTutor]);
    if (!$idTutor || !$stmt->fetchColumn()) $errores[] = 'El tutor seleccionado no existe o no está activo.';

    $stmt = $pdo->prepare('SELECT m.id_carrera FROM materias m WHERE m.id_materia = :id');
    $stmt->execute([':id' => $idMateria]);
    $materia = $stmt->fetch();
    if (!$idMateria || !$materia) {
        $errores[] = 'La materia seleccionada no es válida.';
    } else {
        if ($materia['id_carrera'] !== null && (int) $materia['id_carrera'] !== (int) ($estudiante['id_carrera'] ?? 0)) $errores[] = 'La materia no corresponde a la carrera del estudiante.';
        $stmt = $pdo->prepare('SELECT 1 FROM tutor_materia WHERE id_tutor = :tutor AND id_materia = :materia');
        $stmt->execute([':tutor' => $idTutor, ':materia' => $idMateria]);
        if (!$stmt->fetchColumn()) $errores[] = 'El tutor seleccionado no dicta esta materia.';
    }

    $fechaHora = DateTime::createFromFormat('Y-m-d H:i', $fecha . ' ' . substr($inicio, 0, 5));
    if (!$fechaHora || $fechaHora->format('Y-m-d H:i') !== $fecha . ' ' . substr($inicio, 0, 5) || $fechaHora < (new DateTime())->modify('+' . TUTORIA_ANTICIPACION_HORAS . ' hours')) {
        $errores[] = 'La tutoría debe solicitarse con al menos ' . TUTORIA_ANTICIPACION_HORAS . ' horas de anticipación.';
    }
    // Flujo por BLOQUES horarios (definidos por admin): las horas provienen del bloque,
    // no del estudiante, por lo que no se valida duración libre ni disponibilidad puntual.
    $usaBloque = !empty($datos['id_bloque']);
    if ($usaBloque) {
        $stmt = $pdo->prepare('SELECT 1 FROM bloques_horarios WHERE id_bloque = :id');
        $stmt->execute([':id' => $datos['id_bloque']]);
        if (!$stmt->fetchColumn()) $errores[] = 'El bloque horario seleccionado no existe.';
        if ($fechaHora && (int) $fechaHora->format('N') === 7) $errores[] = 'No se permiten tutorías los domingos.';
    } else {
        // Flujo heredado con hora_inicio/hora_fin libres.
        $iniDt = DateTime::createFromFormat('H:i', substr($inicio, 0, 5));
        $finDt = DateTime::createFromFormat('H:i', substr($fin, 0, 5));
        if (!$iniDt || !$finDt || $finDt <= $iniDt) {
            $errores[] = 'La hora de finalización debe ser posterior a la de inicio.';
        } else {
            $minutos = ($finDt->getTimestamp() - $iniDt->getTimestamp()) / 60;
            if ($minutos < TUTORIA_DURACION_MIN || $minutos > TUTORIA_DURACION_MAX) $errores[] = 'La duración debe estar entre ' . TUTORIA_DURACION_MIN . ' y ' . TUTORIA_DURACION_MAX . ' minutos.';
            if ($fechaHora && (int) $fechaHora->format('N') <= 6) {
                $dias = [1 => 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
                $stmt = $pdo->prepare('SELECT 1 FROM disponibilidad_tutor WHERE id_tutor = :tutor AND dia_semana = :dia AND hora_inicio <= :inicio AND hora_fin >= :fin');
                $stmt->execute([':tutor' => $idTutor, ':dia' => $dias[(int) $fechaHora->format('N')], ':inicio' => $inicio, ':fin' => $fin]);
                if (!$stmt->fetchColumn()) $errores[] = 'El tutor no tiene disponibilidad para el horario solicitado.';
            } elseif ($fechaHora) $errores[] = 'No se permiten tutorías los domingos.';
        }
    }
    // El periodo debe existir en periodos_tutoria (definido por coordinación).
    $stmt = $pdo->prepare('SELECT 1 FROM periodos_tutoria WHERE codigo = :codigo');
    $stmt->execute([':codigo' => (string) ($datos['periodo'] ?? '')]);
    if (!$stmt->fetchColumn()) $errores[] = 'El periodo académico no es válido.';
    return $errores;
}
