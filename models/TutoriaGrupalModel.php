<?php
declare(strict_types=1);

/**
 * Gestiona las sesiones de tutoría grupal y sus estudiantes.
 *
 * Una sesión pertenece a un horario universitario. Los estudiantes se
 * relacionan mediante tutoria_grupal_estudiante para soportar N estudiantes
 * en una misma sesión sin duplicar la información del horario.
 */
class TutoriaGrupalModel
{
    public function __construct(private PDO $pdo) {}

    /** Lista sesiones con materia, tutor y cantidad de estudiantes. */
    public function obtenerTodas(?string $estado = null): array
    {
        $sql = "SELECT tg.*, h.dia_semana,h.turno,h.hora_inicio,h.hora_fin,h.estado AS estado_horario,
                       m.id_materia,m.nombre_materia,c.nombre_carrera,
                       t.id_tutor,u.nombre AS tutor_nombre,u.apellido AS tutor_apellido,
                       COUNT(CASE WHEN ge.estado IN ('aprobada','inscrito','asistio') THEN 1 END) AS total_estudiantes
                FROM tutorias_grupales tg
                INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                INNER JOIN materias m ON m.id_materia=h.id_materia
                INNER JOIN carreras c ON c.id_carrera=m.id_carrera
                INNER JOIN tutores t ON t.id_tutor=h.id_tutor
                INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
                LEFT JOIN tutoria_grupal_estudiante ge ON ge.id_tutoria_grupal=tg.id_tutoria_grupal
                WHERE 1=1";
        $params = [];

        if ($estado !== null && in_array($estado, ['programada','en_curso','realizada','cancelada'], true)) {
            $sql .= ' AND tg.estado=:estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' GROUP BY tg.id_tutoria_grupal ORDER BY tg.fecha DESC,h.hora_inicio DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene las sesiones grupales asignadas a un tutor.
     *
     * Esta consulta se usa en el panel del tutor para que nunca vea
     * sesiones de otros docentes. El filtro se realiza en SQL y no solo
     * en la interfaz.
     *
     * @param int $idTutor Identificador del tutor autenticado.
     * @param string|null $estado Estado opcional de la sesión.
     * @return array Lista de sesiones del tutor.
     */
    public function obtenerParaTutor(int $idTutor, ?string $estado = null): array
    {
        $sql = "SELECT tg.*, h.dia_semana,h.turno,h.hora_inicio,h.hora_fin,
                       h.estado AS estado_horario,
                       m.nombre_materia,c.nombre_carrera,
                       CONCAT(u.nombre,' ',u.apellido) AS tutor,
                       COUNT(CASE WHEN ge.estado IN ('aprobada','inscrito','asistio') THEN 1 END) AS total_estudiantes
                FROM tutorias_grupales tg
                INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                INNER JOIN materias m ON m.id_materia=h.id_materia
                INNER JOIN carreras c ON c.id_carrera=m.id_carrera
                INNER JOIN tutores t ON t.id_tutor=h.id_tutor
                INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
                LEFT JOIN tutoria_grupal_estudiante ge ON ge.id_tutoria_grupal=tg.id_tutoria_grupal
                WHERE h.id_tutor=:tutor";
        $params = [':tutor' => $idTutor];

        if ($estado !== null && in_array($estado, ['programada','en_curso','realizada','cancelada'], true)) {
            $sql .= ' AND tg.estado=:estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' GROUP BY tg.id_tutoria_grupal ORDER BY tg.fecha ASC,h.hora_inicio ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene las sesiones grupales en las que un estudiante está inscrito.
     *
     * Solo se consideran inscripciones activas (inscrito/asistio), de modo
     * que una baja histórica no siga apareciendo como tutoría del estudiante.
     *
     * @param int $idEstudiante Identificador del estudiante autenticado.
     * @param string|null $estado Estado opcional de la sesión.
     * @return array Lista de sesiones del estudiante.
     */
    public function obtenerParaEstudiante(int $idEstudiante, ?string $estado = null): array
    {
        $sql = "SELECT tg.*, h.dia_semana,h.turno,h.hora_inicio,h.hora_fin,
                       h.estado AS estado_horario,
                       m.nombre_materia,c.nombre_carrera,
                       CONCAT(u.nombre,' ',u.apellido) AS tutor,
                       ge.estado AS estado_participacion
                FROM tutorias_grupales tg
                INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                INNER JOIN materias m ON m.id_materia=h.id_materia
                INNER JOIN carreras c ON c.id_carrera=m.id_carrera
                INNER JOIN tutores t ON t.id_tutor=h.id_tutor
                INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
                INNER JOIN tutoria_grupal_estudiante ge
                        ON ge.id_tutoria_grupal=tg.id_tutoria_grupal
                       AND ge.id_estudiante=:estudiante
                       AND ge.estado IN ('aprobada','inscrito','asistio')
                WHERE 1=1";
        $params = [':estudiante' => $idEstudiante];

        if ($estado !== null && in_array($estado, ['programada','en_curso','realizada','cancelada'], true)) {
            $sql .= ' AND tg.estado=:estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' ORDER BY tg.fecha ASC,h.hora_inicio ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Obtiene una sesión completa por ID. */
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT tg.*, h.dia_semana,h.turno,h.hora_inicio,h.hora_fin,h.estado AS estado_horario,
                    h.id_materia,h.id_tutor,m.nombre_materia,c.nombre_carrera,
                    CONCAT(u.nombre,' ',u.apellido) AS tutor
             FROM tutorias_grupales tg
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             INNER JOIN materias m ON m.id_materia=h.id_materia
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             INNER JOIN tutores t ON t.id_tutor=h.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE tg.id_tutoria_grupal=:id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Lista estudiantes inscritos o con asistencia registrada en una sesión. */
    public function estudiantes(int $idTutoriaGrupal): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ge.*,e.id_estudiante,e.registro_universitario,
                    u.nombre,u.apellido,u.correo,c.nombre_carrera,e.semestre
             FROM tutoria_grupal_estudiante ge
             INNER JOIN estudiantes e ON e.id_estudiante=ge.id_estudiante
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN carreras c ON c.id_carrera=e.id_carrera
             WHERE ge.id_tutoria_grupal=:id
             ORDER BY u.apellido,u.nombre"
        );
        $stmt->execute([':id' => $idTutoriaGrupal]);
        return $stmt->fetchAll();
    }

    /** Comprueba que una fecha corresponda al horario lunes-viernes. */
    public function fechaValidaParaHorario(string $fecha): bool
    {
        $timestamp = strtotime($fecha);
        return $timestamp !== false && (int)date('N', $timestamp) >= 1 && (int)date('N', $timestamp) <= 5;
    }

    /** Comprueba que un horario esté activo. */
    public function horarioActivo(int $idHorario): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM horarios_tutoria_grupal
             WHERE id_horario=:id AND estado='activo'"
        );
        $stmt->execute([':id' => $idHorario]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Comprueba que una sesión no exista dos veces para el mismo horario y fecha. */
    public function existeSesion(int $idHorario, string $fecha, ?int $excepto = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM tutorias_grupales WHERE id_horario=:h AND fecha=:f';
        $params = [':h' => $idHorario, ':f' => $fecha];
        if ($excepto !== null) {
            $sql .= ' AND id_tutoria_grupal<>:id';
            $params[':id'] = $excepto;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Valida una sesión antes de crearla o modificarla. */
    public function validarProgramacion(array $d, ?int $excepto = null): array
    {
        $errores = [];
        $idHorario = (int)($d['id_horario'] ?? 0);
        $fecha = (string)($d['fecha'] ?? '');

        if ($idHorario <= 0) {
            $errores[] = 'Selecciona un horario grupal válido.';
            return $errores;
        }
        if (!$this->horarioActivo($idHorario)) {
            $errores[] = 'El horario grupal no existe o está inactivo.';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
            $errores[] = 'La fecha de la tutoría grupal no es válida.';
        } elseif (!$this->fechaValidaParaHorario($fecha)) {
            $errores[] = 'Las tutorías grupales solo pueden programarse de lunes a viernes.';
        }
        if (!$errores) {
            // La fecha de la sesión debe coincidir con el día definido
            // por el horario institucional (por ejemplo, un horario de
            // miércoles no puede generar una sesión para jueves).
            $stmt = $this->pdo->prepare(
                'SELECT dia_semana FROM horarios_tutoria_grupal WHERE id_horario=:id LIMIT 1'
            );
            $stmt->execute([':id' => $idHorario]);
            $horario = $stmt->fetch();

            if (!$horario) {
                $errores[] = 'No se pudo comprobar el día del horario universitario.';
            } else {
                $diasNumericos = [
                    'Lunes' => 1,
                    'Martes' => 2,
                    'Miercoles' => 3,
                    'Jueves' => 4,
                    'Viernes' => 5,
                ];
                $diaEsperado = $diasNumericos[(string)$horario['dia_semana']] ?? 0;
                $diaSeleccionado = (int)date('N', strtotime($fecha));

                if ($diaEsperado !== $diaSeleccionado) {
                    $errores[] = 'La fecha seleccionada no corresponde al día del horario universitario.';
                }
            }
        }

        if (!$errores && $this->existeSesion($idHorario, $fecha, $excepto)) {
            $errores[] = 'Ya existe una sesión para ese horario y fecha.';
        }

        return $errores;
    }

    /** Crea una sesión grupal. */
    public function crear(array $d): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO tutorias_grupales(id_horario,fecha,estado,observaciones)
             VALUES(:h,:f,:estado,:o)'
        )->execute([
            ':h' => $d['id_horario'], ':f' => $d['fecha'],
            ':estado' => $d['estado'] ?? 'programada',
            ':o' => trim((string)($d['observaciones'] ?? '')) ?: null,
        ]);
    }

    /** Actualiza una sesión existente. */
    public function actualizar(int $id, array $d): bool
    {
        return $this->pdo->prepare(
            'UPDATE tutorias_grupales
             SET id_horario=:h,fecha=:f,estado=:estado,observaciones=:o
             WHERE id_tutoria_grupal=:id'
        )->execute([
            ':h' => $d['id_horario'], ':f' => $d['fecha'],
            ':estado' => $d['estado'], ':o' => trim((string)($d['observaciones'] ?? '')) ?: null,
            ':id' => $id,
        ]);
    }

    /** Cambia únicamente el estado de una sesión. */
    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, ['programada','en_curso','realizada','cancelada'], true)) {
            return false;
        }
        return $this->pdo->prepare(
            'UPDATE tutorias_grupales SET estado=:estado WHERE id_tutoria_grupal=:id'
        )->execute([':estado' => $estado, ':id' => $id]);
    }

    /**
     * Cancela una sesión grupal conservando el motivo y el usuario que realizó
     * la acción para mantener trazabilidad administrativa.
     */
    public function cancelar(int $id, string $motivo, int $usuario): bool
    {
        return $this->pdo->prepare(
            "UPDATE tutorias_grupales
             SET estado='cancelada', motivo_cancelacion=:motivo,
                 cancelado_por_usuario=:usuario, fecha_cancelacion=CURRENT_TIMESTAMP
             WHERE id_tutoria_grupal=:id"
        )->execute([
            ':motivo' => trim($motivo),
            ':usuario' => $usuario,
            ':id' => $id,
        ]);
    }

    /**
     * Registra una solicitud enviada por el estudiante.
     *
     * La solicitud nace como pendiente y no cuenta como participante aprobado
     * hasta que administración la valide. Si el estudiante había retirado una
     * solicitud anterior, puede volver a solicitarla.
     */
    public function solicitarInscripcion(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tutoria_grupal_estudiante(id_tutoria_grupal,id_estudiante,estado)
             VALUES(:t,:e,'pendiente')
             ON DUPLICATE KEY UPDATE
                estado=IF(estado='retirado','pendiente',estado),
                fecha_actualizacion=CURRENT_TIMESTAMP"
        );
        return $stmt->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

    /**
     * Inscripción directa realizada por administración.
     *
     * A diferencia de la solicitud del estudiante, esta acción deja al
     * participante directamente en estado aprobado.
     */
    public function inscribirEstudiante(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tutoria_grupal_estudiante(id_tutoria_grupal,id_estudiante,estado)
             VALUES(:t,:e,'aprobada')
             ON DUPLICATE KEY UPDATE
                estado=IF(estado IN ('pendiente','retirado','inscrito'),'aprobada',estado),
                fecha_actualizacion=CURRENT_TIMESTAMP"
        );
        return $stmt->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

    /**
     * Valida una solicitud pendiente antes de aprobarla.
     *
     * La comprobación evita aprobar una solicitud que ya no corresponda a una
     * sesión activa, a un estudiante válido o que genere un cruce con otra
     * tutoría grupal ya aprobada.
     *
     * @return string[] Lista de errores; vacía cuando puede aprobarse.
     */
    public function validarAprobacion(int $idTutoriaGrupal, int $idEstudiante): array
    {
        $errores = [];

        $stmt = $this->pdo->prepare(
            "SELECT tg.estado,h.estado AS estado_horario,tg.fecha,
                    h.hora_inicio,h.hora_fin
             FROM tutorias_grupales tg
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             WHERE tg.id_tutoria_grupal=:id
             LIMIT 1"
        );
        $stmt->execute([':id'=>$idTutoriaGrupal]);
        $sesion = $stmt->fetch();

        if (!$sesion) return ['La tutoría grupal no existe.'];
        if ((string)$sesion['estado'] !== 'programada') {
            $errores[] = 'Solo se pueden aprobar solicitudes mientras la sesión esté programada.';
        }
        if ((string)$sesion['estado_horario'] !== 'activo') {
            $errores[] = 'El horario institucional está inactivo.';
        }

        $estadoActual = $this->estadoParticipacion($idTutoriaGrupal, $idEstudiante);
        if ($estadoActual !== 'pendiente') {
            $errores[] = 'La solicitud seleccionada ya no está pendiente.';
        }

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM estudiantes e
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN roles r ON r.id_rol=u.id_rol
             WHERE e.id_estudiante=:id
               AND u.estado='activo'
               AND r.nombre_rol='estudiante'"
        );
        $stmt->execute([':id'=>$idEstudiante]);
        if ((int)$stmt->fetchColumn() === 0) {
            $errores[] = 'El estudiante no está activo o no corresponde a un usuario estudiante.';
        }

        if (!$errores) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*)
                 FROM tutoria_grupal_estudiante ge
                 INNER JOIN tutorias_grupales tg2 ON tg2.id_tutoria_grupal=ge.id_tutoria_grupal
                 INNER JOIN horarios_tutoria_grupal h2 ON h2.id_horario=tg2.id_horario
                 WHERE ge.id_estudiante=:estudiante
                   AND ge.estado IN ('aprobada','inscrito','asistio')
                   AND tg2.fecha=:fecha
                   AND tg2.estado IN ('programada','en_curso')
                   AND h2.hora_inicio < :hora_fin
                   AND h2.hora_fin > :hora_inicio
                   AND tg2.id_tutoria_grupal<>:tutoria"
            );
            $stmt->execute([
                ':estudiante'=>$idEstudiante,
                ':fecha'=>$sesion['fecha'],
                ':hora_fin'=>$sesion['hora_fin'],
                ':hora_inicio'=>$sesion['hora_inicio'],
                ':tutoria'=>$idTutoriaGrupal,
            ]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errores[] = 'No se puede aprobar porque el estudiante ya tiene otra tutoría grupal en ese mismo horario.';
            }
        }

        return $errores;
    }

    /** Aprueba una solicitud pendiente desde administración. */
    public function aprobarSolicitud(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        return $this->pdo->prepare(
            "UPDATE tutoria_grupal_estudiante
             SET estado='aprobada',fecha_actualizacion=CURRENT_TIMESTAMP
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e AND estado='pendiente'"
        )->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

    /** Obtiene el estado actual de participación del estudiante. */
    public function estadoParticipacion(int $idTutoriaGrupal, int $idEstudiante): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT estado FROM tutoria_grupal_estudiante
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e
             LIMIT 1'
        );
        $stmt->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
        $estado = $stmt->fetchColumn();
        return $estado === false ? null : (string)$estado;
    }

    /** Cambia el estado de participación de un estudiante. */
    public function cambiarEstadoEstudiante(int $idTutoriaGrupal, int $idEstudiante, string $estado): bool
    {
        if (!in_array($estado, ['pendiente','aprobada','inscrito','retirado','asistio','no_asistio'], true)) {
            return false;
        }
        return $this->pdo->prepare(
            'UPDATE tutoria_grupal_estudiante
             SET estado=:estado,fecha_actualizacion=CURRENT_TIMESTAMP
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e'
        )->execute([':estado' => $estado, ':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

    /** Comprueba si un estudiante ya tiene una participación activa/aprobada. */
    public function estudianteInscrito(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tutoria_grupal_estudiante
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e
               AND estado IN ('aprobada','inscrito','asistio')"
        );
        $stmt->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Devuelve estudiantes activos que pueden ser gestionados en una sesión.
     * Se excluyen los que ya están inscritos activamente en la misma sesión.
     */
    public function estudiantesDisponibles(int $idTutoriaGrupal): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT e.id_estudiante,e.registro_universitario,e.semestre,
                    u.id_usuario,u.nombre,u.apellido,u.correo,c.nombre_carrera
             FROM estudiantes e
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN roles r ON r.id_rol=u.id_rol
             INNER JOIN carreras c ON c.id_carrera=e.id_carrera
             LEFT JOIN tutoria_grupal_estudiante ge
                    ON ge.id_estudiante=e.id_estudiante
                   AND ge.id_tutoria_grupal=:tutoria
                   AND ge.estado IN ('pendiente','aprobada','inscrito','asistio')
             WHERE u.estado='activo'
               AND r.nombre_rol='estudiante'
               AND ge.id_estudiante IS NULL
             ORDER BY u.apellido,u.nombre"
        );
        $stmt->execute([':tutoria' => $idTutoriaGrupal]);
        return $stmt->fetchAll();
    }

    /**
     * Valida las reglas de negocio para inscribir a un estudiante.
     *
     * @return string[] Lista de errores. Vacía cuando la inscripción es válida.
     */
    public function validarInscripcion(int $idTutoriaGrupal, int $idEstudiante): array
    {
        $errores = [];

        $stmt = $this->pdo->prepare(
            "SELECT tg.estado,h.estado AS estado_horario,tg.fecha,
                    h.hora_inicio,h.hora_fin
             FROM tutorias_grupales tg
             INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
             WHERE tg.id_tutoria_grupal=:id
             LIMIT 1"
        );
        $stmt->execute([':id' => $idTutoriaGrupal]);
        $sesion = $stmt->fetch();

        if (!$sesion) {
            return ['La tutoría grupal no existe.'];
        }
        if ((string)$sesion['estado'] !== 'programada') {
            $errores[] = 'Solo puedes inscribirte en sesiones programadas.';
        }
        if ((string)$sesion['estado_horario'] !== 'activo') {
            $errores[] = 'El horario institucional está inactivo.';
        }

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM estudiantes e
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN roles r ON r.id_rol=u.id_rol
             WHERE e.id_estudiante=:id
               AND u.estado='activo'
               AND r.nombre_rol='estudiante'"
        );
        $stmt->execute([':id' => $idEstudiante]);
        if ((int)$stmt->fetchColumn() === 0) {
            $errores[] = 'El estudiante no está activo o no corresponde a un usuario estudiante.';
        }

        if (!$errores) {
            $estadoActual = $this->estadoParticipacion($idTutoriaGrupal, $idEstudiante);
            if ($estadoActual === 'pendiente') {
                $errores[] = 'Ya existe una solicitud pendiente para esta tutoría grupal.';
            } elseif (in_array($estadoActual, ['aprobada','inscrito','asistio'], true)) {
                $errores[] = 'El estudiante ya tiene una inscripción aprobada en esta tutoría grupal.';
            }
        }

        if (!$errores) {
            // Un estudiante no puede estar en dos sesiones que se crucen el
            // mismo día. Los bloques institucionales son fijos, pero esta
            // comprobación mantiene la regla incluso si la oferta cambia.
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*)
                 FROM tutoria_grupal_estudiante ge
                 INNER JOIN tutorias_grupales tg2 ON tg2.id_tutoria_grupal=ge.id_tutoria_grupal
                 INNER JOIN horarios_tutoria_grupal h2 ON h2.id_horario=tg2.id_horario
                 INNER JOIN tutorias_grupales tg ON tg.id_tutoria_grupal=:tutoria
                 INNER JOIN horarios_tutoria_grupal h ON h.id_horario=tg.id_horario
                 WHERE ge.id_estudiante=:estudiante
                   AND ge.estado IN ('aprobada','inscrito','asistio')
                   AND tg2.fecha=tg.fecha
                   AND tg2.estado IN ('programada','en_curso')
                   AND h2.hora_inicio < h.hora_fin
                   AND h2.hora_fin > h.hora_inicio"
            );
            $stmt->execute([
                ':tutoria' => $idTutoriaGrupal,
                ':estudiante' => $idEstudiante,
            ]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errores[] = 'El estudiante ya tiene otra tutoría grupal en ese mismo horario.';
            }
        }

        return $errores;
    }

    /**
     * Retira una solicitud pendiente conservando el historial.
     *
     * Una inscripción ya aprobada no puede retirarse desde el flujo del
     * estudiante; después de la aprobación desaparece el botón "Retirarme".
     */
    public function retirarSolicitud(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        return $this->pdo->prepare(
            "UPDATE tutoria_grupal_estudiante
             SET estado='retirado',fecha_actualizacion=CURRENT_TIMESTAMP
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e AND estado='pendiente'"
        )->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

    /** Retira administrativamente una inscripción aprobada. */
    public function retirarEstudiante(int $idTutoriaGrupal, int $idEstudiante): bool
    {
        return $this->pdo->prepare(
            "UPDATE tutoria_grupal_estudiante
             SET estado='retirado',fecha_actualizacion=CURRENT_TIMESTAMP
             WHERE id_tutoria_grupal=:t AND id_estudiante=:e
               AND estado IN ('aprobada','inscrito')"
        )->execute([':t' => $idTutoriaGrupal, ':e' => $idEstudiante]);
    }

}
