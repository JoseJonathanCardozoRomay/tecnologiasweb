<?php

require_once __DIR__ . '/NotificacionModel.php';
require_once __DIR__ . '/BitacoraModel.php';

class SolicitudTutoriaModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Busca el perfil académico asociado a una cuenta. */
    public function buscarEstudiantePorUsuario(
        int $idUsuario
    ): ?array {
        $consulta = $this->conexion->prepare("
            SELECT id_estudiante, id_usuario
            FROM estudiantes
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ");

        $consulta->execute(['id_usuario' => $idUsuario]);
        $estudiante = $consulta->fetch(PDO::FETCH_ASSOC);

        return $estudiante ?: null;
    }

    /** Busca el perfil de tutor asociado a una cuenta. */
    public function buscarTutorPorUsuario(
        int $idUsuario
    ): ?array {
        $consulta = $this->conexion->prepare("
            SELECT id_tutor, id_usuario
            FROM tutores
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ");

        $consulta->execute(['id_usuario' => $idUsuario]);
        $tutor = $consulta->fetch(PDO::FETCH_ASSOC);

        return $tutor ?: null;
    }

    /** Obtiene el periodo abierto para nuevas solicitudes. */
    public function obtenerPeriodoAbierto(): ?array
    {
        $consulta = $this->conexion->query("
            SELECT
                id_periodo,
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin
            FROM periodos_inscripcion
            WHERE estado = 'abierto'
                AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_fin
            ORDER BY fecha_inicio DESC, id_periodo DESC
            LIMIT 1
        ");

        $periodo = $consulta->fetch(PDO::FETCH_ASSOC);

        return $periodo ?: null;
    }

    /** Lista materias y tutores que tienen cupos configurados en el periodo. */
    public function listarOferta(int $idPeriodo): array
    {
        $consulta = $this->conexion->prepare("
            SELECT
                m.id_materia,
                m.nombre_materia,
                t.id_tutor,
                t.especialidad,
                u.nombre,
                u.apellido,
                tp.cupo_maximo,
                (
                    SELECT COUNT(DISTINCT tu.id_estudiante)
                    FROM tutorias AS tu
                    INNER JOIN periodos_inscripcion AS p
                        ON p.id_periodo = tp.id_periodo
                    WHERE tu.id_tutor = t.id_tutor
                        AND tu.fecha_solicitud >= p.fecha_inicio
                        AND tu.fecha_solicitud < DATE_ADD(
                            p.fecha_fin,
                            INTERVAL 1 DAY
                        )
                        AND tu.estado IN (
                            'pendiente_aprobacion',
                            'observada',
                            'confirmada',
                            'realizada'
                        )
                ) AS cupos_ocupados
            FROM tutor_periodo AS tp
            INNER JOIN tutores AS t
                ON t.id_tutor = tp.id_tutor
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            INNER JOIN tutor_materia AS tm
                ON tm.id_tutor = t.id_tutor
            INNER JOIN materias AS m
                ON m.id_materia = tm.id_materia
            WHERE tp.id_periodo = :id_periodo
                AND tp.activo = 1
                AND u.estado = 'activo'
            ORDER BY m.nombre_materia, u.nombre, u.apellido
        ");

        $consulta->execute(['id_periodo' => $idPeriodo]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista el historial de solicitudes y evaluaciones del estudiante. */
    public function listarPorEstudiante(int $idEstudiante): array
    {
        $consulta = $this->conexion->prepare("
            SELECT
                tu.id_tutoria,
                tu.fecha,
                tu.hora_inicio,
                tu.hora_fin,
                tu.modalidad,
                tu.lugar_o_enlace,
                tu.estado,
                tu.observaciones,
                tu.observacion_revision,
                tu.fecha_solicitud,
                ev.calificacion,
                ev.comentario AS comentario_evaluacion,
                m.nombre_materia,
                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor
            FROM tutorias AS tu
            INNER JOIN materias AS m
                ON m.id_materia = tu.id_materia
            LEFT JOIN evaluaciones_tutoria AS ev
                ON ev.id_tutoria = tu.id_tutoria
            INNER JOIN tutores AS t
                ON t.id_tutor = tu.id_tutor
            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario
            WHERE tu.id_estudiante = :id_estudiante
            ORDER BY tu.fecha_solicitud DESC, tu.id_tutoria DESC
        ");

        $consulta->execute(['id_estudiante' => $idEstudiante]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista solicitudes que el tutor debe aceptar o corregir. */
    public function listarPendientesTutor(int $idUsuario): array
    {
        $consulta = $this->conexion->prepare("
            SELECT
                tu.id_tutoria,
                tu.id_tutor,
                tu.id_materia,
                tu.fecha,
                tu.hora_inicio,
                tu.hora_fin,
                tu.modalidad,
                tu.lugar_o_enlace,
                tu.estado,
                tu.observacion_revision,
                tu.fecha_solicitud,
                m.nombre_materia,
                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante
            FROM tutorias AS tu
            INNER JOIN tutores AS t
                ON t.id_tutor = tu.id_tutor
            INNER JOIN estudiantes AS e
                ON e.id_estudiante = tu.id_estudiante
            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario
            INNER JOIN materias AS m
                ON m.id_materia = tu.id_materia
            WHERE t.id_usuario = :id_usuario
                AND tu.estado IN ('pendiente', 'pendiente_tutor', 'observada')
            ORDER BY tu.fecha_solicitud ASC, tu.id_tutoria ASC
        ");

        $consulta->execute(['id_usuario' => $idUsuario]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista las tutorías aprobadas del tutor en la sección "Mis materias". */
    public function listarAprobadasPorTutor(int $idUsuario): array
    {
        $consulta = $this->conexion->prepare("
            SELECT
                tu.id_tutoria,
                tu.fecha,
                tu.hora_inicio,
                tu.hora_fin,
                tu.modalidad,
                tu.lugar_o_enlace,
                tu.estado,
                tu.observaciones,
                tu.fecha_solicitud,
                ev.calificacion,
                m.nombre_materia,
                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante
            FROM tutorias AS tu
            INNER JOIN tutores AS t
                ON t.id_tutor = tu.id_tutor
            INNER JOIN estudiantes AS e
                ON e.id_estudiante = tu.id_estudiante
            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario
            INNER JOIN materias AS m
                ON m.id_materia = tu.id_materia
            LEFT JOIN evaluaciones_tutoria AS ev
                ON ev.id_tutoria = tu.id_tutoria
            WHERE t.id_usuario = :id_usuario
                AND tu.estado IN ('confirmada', 'realizada')
            ORDER BY tu.fecha DESC, tu.hora_inicio DESC, tu.id_tutoria DESC
        ");

        $consulta->execute(['id_usuario' => $idUsuario]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista propuestas del tutor que esperan revisión administrativa. */
    public function listarPendientesAprobacion(): array
    {
        $consulta = $this->conexion->query("
            SELECT
                tu.id_tutoria,
                tu.fecha,
                tu.hora_inicio,
                tu.hora_fin,
                tu.modalidad,
                tu.lugar_o_enlace,
                tu.fecha_solicitud,
                m.nombre_materia,
                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,
                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor
            FROM tutorias AS tu
            INNER JOIN estudiantes AS e
                ON e.id_estudiante = tu.id_estudiante
            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario
            INNER JOIN tutores AS t
                ON t.id_tutor = tu.id_tutor
            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario
            INNER JOIN materias AS m
                ON m.id_materia = tu.id_materia
            WHERE tu.estado = 'pendiente_aprobacion'
            ORDER BY tu.fecha_solicitud ASC, tu.id_tutoria ASC
        ");

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Registra una solicitud del estudiante sin proponer horario. */
    public function crearSolicitud(
        int $idEstudiante,
        int $idTutor,
        int $idMateria
    ): string {
        if ($idEstudiante < 1 || $idTutor < 1 || $idMateria < 1) {
            return 'datos_invalidos';
        }

        try {
            $this->conexion->beginTransaction();

            $consultaPeriodo = $this->conexion->query("
                SELECT id_periodo, fecha_inicio, fecha_fin
                FROM periodos_inscripcion
                WHERE estado = 'abierto'
                    AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_fin
                ORDER BY fecha_inicio DESC, id_periodo DESC
                LIMIT 1
                FOR UPDATE
            ");

            $periodo = $consultaPeriodo->fetch(PDO::FETCH_ASSOC);

            if (!$periodo) {
                $this->conexion->rollBack();
                return 'periodo_cerrado';
            }

            $consultaCupo = $this->conexion->prepare("
                SELECT cupo_maximo
                FROM tutor_periodo
                WHERE id_tutor = :id_tutor
                    AND id_periodo = :id_periodo
                    AND activo = 1
                LIMIT 1
                FOR UPDATE
            ");

            $consultaCupo->execute([
                'id_tutor' => $idTutor,
                'id_periodo' => (int) $periodo['id_periodo']
            ]);

            if ($consultaCupo->fetchColumn() === false) {
                $this->conexion->rollBack();
                return 'tutor_no_disponible';
            }

            $consultaMateria = $this->conexion->prepare("
                SELECT COUNT(*)
                FROM tutor_materia AS tm
                INNER JOIN tutores AS t
                    ON t.id_tutor = tm.id_tutor
                INNER JOIN usuarios AS u
                    ON u.id_usuario = t.id_usuario
                WHERE tm.id_tutor = :id_tutor
                    AND tm.id_materia = :id_materia
                    AND u.estado = 'activo'
            ");

            $consultaMateria->execute([
                'id_tutor' => $idTutor,
                'id_materia' => $idMateria
            ]);

            if ((int) $consultaMateria->fetchColumn() !== 1) {
                $this->conexion->rollBack();
                return 'tutor_no_disponible';
            }

            $consultaDuplicada = $this->conexion->prepare("
                SELECT COUNT(*)
                FROM tutorias AS tu
                WHERE tu.id_estudiante = :id_estudiante
                    AND tu.id_tutor = :id_tutor
                    AND tu.id_materia = :id_materia
                    AND tu.fecha_solicitud >= :fecha_inicio
                    AND tu.fecha_solicitud < DATE_ADD(
                        :fecha_fin,
                        INTERVAL 1 DAY
                    )
                    AND tu.estado NOT IN ('cancelada', 'rechazada')
            ");

            $consultaDuplicada->execute([
                'id_estudiante' => $idEstudiante,
                'id_tutor' => $idTutor,
                'id_materia' => $idMateria,
                'fecha_inicio' => $periodo['fecha_inicio'],
                'fecha_fin' => $periodo['fecha_fin']
            ]);

            if ((int) $consultaDuplicada->fetchColumn() > 0) {
                $this->conexion->rollBack();
                return 'solicitud_existente';
            }

            $consultaInsertar = $this->conexion->prepare("
                INSERT INTO tutorias (
                    id_estudiante,
                    id_tutor,
                    id_materia,
                    fecha,
                    hora_inicio,
                    hora_fin,
                    modalidad,
                    lugar_o_enlace,
                    observaciones,
                    estado
                )
                VALUES (
                    :id_estudiante,
                    :id_tutor,
                    :id_materia,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    'pendiente_tutor'
                )
            ");

            $consultaInsertar->execute([
                'id_estudiante' => $idEstudiante,
                'id_tutor' => $idTutor,
                'id_materia' => $idMateria
            ]);

            $consultaTutor = $this->conexion->prepare("
                SELECT id_usuario
                FROM tutores
                WHERE id_tutor = :id_tutor
                LIMIT 1
            ");
            $consultaTutor->execute(['id_tutor' => $idTutor]);
            $idUsuarioTutor = (int) $consultaTutor->fetchColumn();

            if ($idUsuarioTutor > 0) {
                $this->crearNotificacion(
                    $idUsuarioTutor,
                    'solicitud_tutoria',
                    'Nueva solicitud de tutoría',
                    'Tienes una nueva solicitud de tutoría por revisar.',
                    'controllers/solicitudes_tutor.php'
                );
            }

            $this->conexion->commit();

            return 'guardado';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                'Error al registrar solicitud de tutoría: '
                . $error->getMessage()
            );

            return 'error';
        }
    }

    /**
     * El tutor acepta o rechaza una solicitud.
     * Al aceptar, propone los datos para que los revise coordinación.
     */
    public function responderTutor(
        int $idTutoria,
        int $idUsuarioTutor,
        string $accion,
        ?string $fecha = null,
        ?string $horaInicio = null,
        ?string $horaFin = null,
        ?string $modalidad = null,
        ?string $lugarOEnlace = null
    ): string {
        if (
            $idTutoria < 1
            || $idUsuarioTutor < 1
            || !in_array($accion, ['aceptar', 'rechazar'], true)
        ) {
            return 'datos_invalidos';
        }

        if ($accion === 'rechazar') {
            try {
                $this->conexion->beginTransaction();

                $consultaEstudiante = $this->conexion->prepare("
                    SELECT es.id_usuario, tu.estado
                    FROM tutorias AS tu
                    INNER JOIN tutores AS t
                        ON t.id_tutor = tu.id_tutor
                    INNER JOIN estudiantes AS es
                        ON es.id_estudiante = tu.id_estudiante
                    WHERE tu.id_tutoria = :id_tutoria
                        AND t.id_usuario = :id_usuario
                        AND tu.estado IN ('pendiente', 'pendiente_tutor', 'observada')
                    LIMIT 1
                    FOR UPDATE
                ");
                $consultaEstudiante->execute([
                    'id_tutoria' => $idTutoria,
                    'id_usuario' => $idUsuarioTutor
                ]);
                $solicitudRechazada = $consultaEstudiante->fetch(PDO::FETCH_ASSOC);
                $idUsuarioEstudiante = (int) (
                    $solicitudRechazada['id_usuario'] ?? 0
                );

                if ($idUsuarioEstudiante < 1) {
                    $this->conexion->rollBack();
                    return 'ya_procesada';
                }

                $consulta = $this->conexion->prepare("
                    UPDATE tutorias AS tu
                    INNER JOIN tutores AS t
                        ON t.id_tutor = tu.id_tutor
                    SET tu.estado = 'rechazada'
                    WHERE tu.id_tutoria = :id_tutoria
                        AND t.id_usuario = :id_usuario
                        AND tu.estado IN ('pendiente', 'pendiente_tutor', 'observada')
                ");
                $consulta->execute([
                    'id_tutoria' => $idTutoria,
                    'id_usuario' => $idUsuarioTutor
                ]);

                if ($consulta->rowCount() !== 1) {
                    $this->conexion->rollBack();
                    return 'ya_procesada';
                }

                $this->crearNotificacion(
                    $idUsuarioEstudiante,
                    'solicitud_rechazada',
                    'Solicitud de tutoría rechazada',
                    'El tutor no pudo aceptar tu solicitud. Puedes enviar otra solicitud si lo necesitas.',
                    'controllers/solicitudes_crear.php'
                );

                $this->registrarCambio(
                    $idUsuarioTutor,
                    $idTutoria,
                    'solicitud_tutoria_rechazada',
                    (string) $solicitudRechazada['estado'],
                    'rechazada'
                );

                $this->conexion->commit();
                return 'rechazada';
            } catch (Throwable $error) {
                if ($this->conexion->inTransaction()) {
                    $this->conexion->rollBack();
                }

                error_log(
                    'Error al rechazar solicitud de tutoría: '
                    . $error->getMessage()
                );
                return 'error';
            }
        }

        if (
            !$this->horarioValido(
                $fecha ?? '',
                $horaInicio ?? '',
                $horaFin ?? ''
            )
            || !in_array($modalidad, ['presencial', 'virtual'], true)
            || mb_strlen($lugarOEnlace ?? '') > 200
            || (
                $modalidad === 'presencial'
                && trim($lugarOEnlace ?? '') === ''
            )
        ) {
            return 'datos_invalidos';
        }

        try {
            $this->conexion->beginTransaction();

            $consultaSolicitud = $this->conexion->prepare("
                SELECT
                    tu.id_tutoria,
                    tu.id_tutor,
                    tu.id_estudiante,
                    tu.id_materia,
                    tu.estado,
                    p.id_periodo,
                    p.fecha_inicio,
                    p.fecha_fin,
                    tp.cupo_maximo
                FROM tutorias AS tu
                INNER JOIN tutores AS t
                    ON t.id_tutor = tu.id_tutor
                INNER JOIN periodos_inscripcion AS p
                    ON tu.fecha_solicitud >= p.fecha_inicio
                    AND tu.fecha_solicitud < DATE_ADD(
                        p.fecha_fin,
                        INTERVAL 1 DAY
                    )
                INNER JOIN tutor_periodo AS tp
                    ON tp.id_tutor = tu.id_tutor
                    AND tp.id_periodo = p.id_periodo
                    AND tp.activo = 1
                WHERE tu.id_tutoria = :id_tutoria
                    AND t.id_usuario = :id_usuario
                    AND tu.estado IN ('pendiente', 'pendiente_tutor', 'observada')
                LIMIT 1
                FOR UPDATE
            ");

            $consultaSolicitud->execute([
                'id_tutoria' => $idTutoria,
                'id_usuario' => $idUsuarioTutor
            ]);

            $solicitud = $consultaSolicitud->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                $this->conexion->rollBack();
                return 'ya_procesada';
            }

            $consultaMateria = $this->conexion->prepare("
                SELECT COUNT(*)
                FROM tutor_materia
                WHERE id_tutor = :id_tutor
                    AND id_materia = :id_materia
            ");

            $consultaMateria->execute([
                'id_tutor' => (int) $solicitud['id_tutor'],
                'id_materia' => (int) $solicitud['id_materia']
            ]);

            if ((int) $consultaMateria->fetchColumn() !== 1) {
                $this->conexion->rollBack();
                return 'tutor_no_disponible';
            }

            $consultaOcupacion = $this->conexion->prepare("
                SELECT
                    COUNT(DISTINCT tu.id_estudiante) AS estudiantes,
                    MAX(tu.id_estudiante = :id_estudiante) AS ya_asignado
                FROM tutorias AS tu
                WHERE tu.id_tutor = :id_tutor
                    AND tu.id_tutoria <> :id_tutoria
                    AND tu.fecha_solicitud >= :fecha_inicio
                    AND tu.fecha_solicitud < DATE_ADD(
                        :fecha_fin,
                        INTERVAL 1 DAY
                    )
                    AND tu.estado IN (
                        'pendiente_aprobacion',
                        'observada',
                        'confirmada',
                        'realizada'
                    )
            ");

            $consultaOcupacion->execute([
                'id_estudiante' => (int) $solicitud['id_estudiante'],
                'id_tutor' => (int) $solicitud['id_tutor'],
                'id_tutoria' => $idTutoria,
                'fecha_inicio' => $solicitud['fecha_inicio'],
                'fecha_fin' => $solicitud['fecha_fin']
            ]);

            $ocupacion = $consultaOcupacion->fetch(PDO::FETCH_ASSOC);

            if (
                (int) $ocupacion['estudiantes']
                    >= (int) $solicitud['cupo_maximo']
                && (int) $ocupacion['ya_asignado'] !== 1
            ) {
                $this->conexion->rollBack();
                return 'sin_cupo';
            }

            $consultaCruce = $this->conexion->prepare("
                SELECT COUNT(*)
                FROM tutorias AS tu
                WHERE tu.id_tutoria <> :id_tutoria
                    AND tu.fecha = :fecha
                    AND (
                        tu.id_tutor = :id_tutor
                        OR tu.id_estudiante = :id_estudiante
                    )
                    AND tu.estado IN (
                        'pendiente_aprobacion',
                        'observada',
                        'confirmada'
                    )
                    AND tu.hora_inicio < :hora_fin
                    AND tu.hora_fin > :hora_inicio
            ");

            $consultaCruce->execute([
                'id_tutoria' => $idTutoria,
                'fecha' => $fecha,
                'id_tutor' => (int) $solicitud['id_tutor'],
                'id_estudiante' => (int) $solicitud['id_estudiante'],
                'hora_fin' => $horaFin,
                'hora_inicio' => $horaInicio
            ]);

            if ((int) $consultaCruce->fetchColumn() > 0) {
                $this->conexion->rollBack();
                return 'horario_ocupado';
            }

            $consultaActualizar = $this->conexion->prepare("
                UPDATE tutorias
                SET
                    fecha = :fecha,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    modalidad = :modalidad,
                    lugar_o_enlace = :lugar_o_enlace,
                    estado = 'pendiente_aprobacion',
                    observacion_revision = NULL
                WHERE id_tutoria = :id_tutoria
                    AND estado IN ('pendiente', 'pendiente_tutor', 'observada')
            ");

            $consultaActualizar->execute([
                'fecha' => $fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'modalidad' => $modalidad,
                'lugar_o_enlace' => trim($lugarOEnlace ?? '') !== ''
                    ? trim($lugarOEnlace)
                    : null,
                'id_tutoria' => $idTutoria
            ]);

            if ($consultaActualizar->rowCount() !== 1) {
                $this->conexion->rollBack();
                return 'ya_procesada';
            }

            $this->notificarPersonalGestion(
                'tutoria_por_aprobar',
                'Tutoría pendiente de aprobación',
                'Un tutor propuso el horario y la modalidad de una tutoría.',
                'controllers/solicitudes_listar.php'
            );

            $this->registrarCambio(
                $idUsuarioTutor,
                $idTutoria,
                'propuesta_tutoria_enviada',
                (string) $solicitud['estado'],
                'pendiente_aprobacion'
            );

            $this->conexion->commit();

            return 'pendiente_aprobacion';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                'Error al responder solicitud de tutoría: '
                . $error->getMessage()
            );

            return 'error';
        }
    }

    /** Administración aprueba una propuesta o la devuelve con observaciones. */
    public function resolverRevision(
        int $idTutoria,
        string $accion,
        ?string $observacion,
        int $idUsuarioRevisor
    ): string {
        if (
            $idTutoria < 1
            || $idUsuarioRevisor < 1
            || !in_array($accion, ['aprobar', 'observar'], true)
        ) {
            return 'datos_invalidos';
        }

        $observacion = trim($observacion ?? '');

        if (
            $accion === 'observar'
            && (
                $observacion === ''
                || mb_strlen($observacion) > 500
            )
        ) {
            return 'observacion_invalida';
        }

        try {
            $this->conexion->beginTransaction();

            $consultaDestinatarios = $this->conexion->prepare("
                SELECT
                    ut.id_usuario AS id_usuario_tutor,
                    ue.id_usuario AS id_usuario_estudiante
                FROM tutorias AS tu
                INNER JOIN tutores AS t
                    ON t.id_tutor = tu.id_tutor
                INNER JOIN usuarios AS ut
                    ON ut.id_usuario = t.id_usuario
                INNER JOIN estudiantes AS e
                    ON e.id_estudiante = tu.id_estudiante
                INNER JOIN usuarios AS ue
                    ON ue.id_usuario = e.id_usuario
                WHERE tu.id_tutoria = :id_tutoria
                    AND tu.estado = 'pendiente_aprobacion'
                LIMIT 1
                FOR UPDATE
            ");
            $consultaDestinatarios->execute([
                'id_tutoria' => $idTutoria
            ]);
            $destinatarios = $consultaDestinatarios->fetch(PDO::FETCH_ASSOC);

            if (!$destinatarios) {
                $this->conexion->rollBack();
                return 'ya_procesada';
            }

            if ($accion === 'aprobar') {
                $consulta = $this->conexion->prepare("
                    UPDATE tutorias
                    SET
                        estado = 'confirmada',
                        observacion_revision = NULL
                    WHERE id_tutoria = :id_tutoria
                        AND estado = 'pendiente_aprobacion'
                        AND fecha IS NOT NULL
                        AND hora_inicio IS NOT NULL
                        AND hora_fin IS NOT NULL
                        AND modalidad IS NOT NULL
                        AND (
                            modalidad = 'virtual'
                            OR (
                                modalidad = 'presencial'
                                AND lugar_o_enlace IS NOT NULL
                                AND TRIM(lugar_o_enlace) <> ''
                            )
                        )
                ");
                $consulta->execute(['id_tutoria' => $idTutoria]);

                if ($consulta->rowCount() !== 1) {
                    $this->conexion->rollBack();
                    return 'ya_procesada';
                }

                $this->crearNotificacion(
                    (int) $destinatarios['id_usuario_tutor'],
                    'tutoria_aprobada',
                    'Tutoría aprobada',
                    'Coordinación aprobó la tutoría. Ya está en tu sección Mis materias.',
                    'controllers/mis_materias_tutor.php'
                );
                $this->crearNotificacion(
                    (int) $destinatarios['id_usuario_estudiante'],
                    'tutoria_aprobada',
                    'Tutoría aprobada',
                    'Coordinación aprobó tu tutoría. Consulta el horario y la modalidad en tus solicitudes.',
                    'controllers/solicitudes_crear.php'
                );

                $this->registrarCambio(
                    $idUsuarioRevisor,
                    $idTutoria,
                    'tutoria_aprobada',
                    'pendiente_aprobacion',
                    'confirmada'
                );

                $this->conexion->commit();
                return 'confirmada';
            }

            $consulta = $this->conexion->prepare("
                UPDATE tutorias
                SET
                    estado = 'observada',
                    observacion_revision = :observacion
                WHERE id_tutoria = :id_tutoria
                    AND estado = 'pendiente_aprobacion'
            ");
            $consulta->execute([
                'observacion' => $observacion,
                'id_tutoria' => $idTutoria
            ]);

            if ($consulta->rowCount() !== 1) {
                $this->conexion->rollBack();
                return 'ya_procesada';
            }

            $this->crearNotificacion(
                (int) $destinatarios['id_usuario_tutor'],
                'tutoria_observada',
                'Tutoría requiere correcciones',
                'Coordinación devolvió la tutoría con observaciones. Revísalas y envía nuevamente la propuesta.',
                'controllers/solicitudes_tutor.php'
            );

            $this->registrarCambio(
                $idUsuarioRevisor,
                $idTutoria,
                'tutoria_observada',
                'pendiente_aprobacion',
                'observada'
            );

            $this->conexion->commit();
            return 'observada';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                'Error al revisar solicitud de tutoría: '
                . $error->getMessage()
            );
            return 'error';
        }
    }

    /** Crea un aviso interno durante la misma transacción del cambio. */
    private function crearNotificacion(
        int $idUsuario,
        string $tipo,
        string $titulo,
        string $mensaje,
        string $urlDestino
    ): void {
        if ($idUsuario < 1) {
            return;
        }

        $modelo = new NotificacionModel($this->conexion);
        $modelo->crearParaUsuario(
            $idUsuario,
            $tipo,
            $titulo,
            $mensaje,
            $urlDestino
        );
    }

    /** Registra la transición en la bitácora del sistema. */
    private function registrarCambio(
        int $idUsuario,
        int $idTutoria,
        string $accion,
        string $estadoAnterior,
        string $estadoNuevo
    ): void {
        $bitacora = new BitacoraModel($this->conexion);
        $bitacora->registrar(
            $idUsuario,
            $accion,
            'tutorias',
            $idTutoria,
            ['estado' => $estadoAnterior],
            ['estado' => $estadoNuevo]
        );
    }

    /** Avisa a las cuentas activas de administración y coordinación MG. */
    private function notificarPersonalGestion(
        string $tipo,
        string $titulo,
        string $mensaje,
        string $urlDestino
    ): void {
        $consulta = $this->conexion->query("
            SELECT u.id_usuario
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
            WHERE r.nombre_rol IN ('administrador', 'coordinador_mg')
                AND u.estado = 'activo'
        ");

        foreach ($consulta->fetchAll(PDO::FETCH_COLUMN) as $idUsuario) {
            $this->crearNotificacion(
                (int) $idUsuario,
                $tipo,
                $titulo,
                $mensaje,
                $urlDestino
            );
        }
    }

    /** El tutor da por realizada una tutoría cuyo horario ya terminó. */
    public function marcarRealizada(
        int $idTutoria,
        int $idUsuarioTutor
    ): string {
        if ($idTutoria < 1 || $idUsuarioTutor < 1) {
            return 'datos_invalidos';
        }

        try {
            $this->conexion->beginTransaction();

            $consulta = $this->conexion->prepare("
                SELECT e.id_usuario
                FROM tutorias AS tu
                INNER JOIN tutores AS t
                    ON t.id_tutor = tu.id_tutor
                INNER JOIN estudiantes AS e
                    ON e.id_estudiante = tu.id_estudiante
                WHERE tu.id_tutoria = :id_tutoria
                    AND t.id_usuario = :id_usuario
                    AND tu.estado = 'confirmada'
                    AND TIMESTAMP(tu.fecha, tu.hora_fin) <= NOW()
                LIMIT 1
                FOR UPDATE
            ");
            $consulta->execute([
                'id_tutoria' => $idTutoria,
                'id_usuario' => $idUsuarioTutor
            ]);
            $idUsuarioEstudiante = (int) $consulta->fetchColumn();

            if ($idUsuarioEstudiante < 1) {
                $this->conexion->rollBack();
                return 'no_disponible';
            }

            $actualizar = $this->conexion->prepare("
                UPDATE tutorias
                SET estado = 'realizada'
                WHERE id_tutoria = :id_tutoria
                    AND estado = 'confirmada'
            ");
            $actualizar->execute(['id_tutoria' => $idTutoria]);

            if ($actualizar->rowCount() !== 1) {
                $this->conexion->rollBack();
                return 'no_disponible';
            }

            $this->crearNotificacion(
                $idUsuarioEstudiante,
                'tutoria_realizada',
                'Tutoría realizada',
                'Tu tutoría fue marcada como realizada. Puedes evaluarla desde Mis solicitudes.',
                'controllers/solicitudes_crear.php'
            );

            $this->registrarCambio(
                $idUsuarioTutor,
                $idTutoria,
                'tutoria_realizada',
                'confirmada',
                'realizada'
            );

            $this->conexion->commit();
            return 'realizada';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log('Error al finalizar tutoría: ' . $error->getMessage());
            return 'error';
        }
    }

    /** Cada estudiante puede evaluar una vez una tutoría realizada propia. */
    public function evaluar(
        int $idTutoria,
        int $idEstudiante,
        int $calificacion,
        string $comentario
    ): string {
        $comentario = trim($comentario);

        if (
            $idTutoria < 1
            || $idEstudiante < 1
            || $calificacion < 1
            || $calificacion > 5
            || mb_strlen($comentario) > 1000
        ) {
            return 'evaluacion_invalida';
        }

        try {
            $this->conexion->beginTransaction();

            $consulta = $this->conexion->prepare("
                SELECT t.id_usuario
                FROM tutorias AS tu
                INNER JOIN tutores AS t
                    ON t.id_tutor = tu.id_tutor
                WHERE tu.id_tutoria = :id_tutoria
                    AND tu.id_estudiante = :id_estudiante
                    AND tu.estado = 'realizada'
                LIMIT 1
                FOR UPDATE
            ");
            $consulta->execute([
                'id_tutoria' => $idTutoria,
                'id_estudiante' => $idEstudiante
            ]);
            $idUsuarioTutor = (int) $consulta->fetchColumn();

            if ($idUsuarioTutor < 1) {
                $this->conexion->rollBack();
                return 'no_disponible';
            }

            $existe = $this->conexion->prepare("
                SELECT COUNT(*)
                FROM evaluaciones_tutoria
                WHERE id_tutoria = :id_tutoria
            ");
            $existe->execute(['id_tutoria' => $idTutoria]);

            if ((int) $existe->fetchColumn() > 0) {
                $this->conexion->rollBack();
                return 'ya_evaluada';
            }

            $insertar = $this->conexion->prepare("
                INSERT INTO evaluaciones_tutoria (
                    id_tutoria,
                    calificacion,
                    comentario
                ) VALUES (
                    :id_tutoria,
                    :calificacion,
                    :comentario
                )
            ");
            $insertar->execute([
                'id_tutoria' => $idTutoria,
                'calificacion' => $calificacion,
                'comentario' => $comentario !== '' ? $comentario : null
            ]);

            $this->crearNotificacion(
                $idUsuarioTutor,
                'tutoria_evaluada',
                'Nueva evaluación de tutoría',
                'Un estudiante evaluó una tutoría realizada. Consulta Mis materias.',
                'controllers/mis_materias_tutor.php'
            );

            $this->conexion->commit();
            return 'evaluada';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log('Error al evaluar tutoría: ' . $error->getMessage());
            return 'error';
        }
    }

    /** Valida fecha futura y rango horario antes de guardar la propuesta. */
    private function horarioValido(
        string $fecha,
        string $horaInicio,
        string $horaFin
    ): bool {
        $fechaValidada = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fecha
        );

        if (
            !$fechaValidada
            || $fechaValidada->format('Y-m-d') !== $fecha
            || $fecha < date('Y-m-d')
            || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $horaInicio)
            || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $horaFin)
            || $horaFin <= $horaInicio
        ) {
            return false;
        }

        return true;
    }
}
