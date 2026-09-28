<?php

class ReunionMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las reuniones de un expediente asignado al tutor.
     */
    public function listarPorExpediente(
        int $idExpediente,
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                r.id_reunion,
                r.id_asignacion,
                r.fecha_reunion,
                r.hora_inicio,
                r.hora_fin,
                r.modalidad,
                r.lugar_enlace,
                r.tema,
                r.acuerdos,
                r.observaciones,
                r.estado,
                r.asistencia_tutor,
                r.asistencia_estudiante,
                r.estado_validacion,
                r.observacion_validacion,
                r.fecha_validacion,
                r.fecha_registro,

                COUNT(ev.id_evidencia) AS total_evidencias

            FROM reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN cartas_designacion_mg AS c
                ON c.id_asignacion = a.id_asignacion
                AND c.estado = 'aceptada'

            LEFT JOIN evidencias_reunion_mg AS ev
                ON ev.id_reunion = r.id_reunion

            WHERE a.id_expediente = :id_expediente
                AND t.id_usuario = :id_usuario

            GROUP BY
                r.id_reunion,
                r.id_asignacion,
                r.fecha_reunion,
                r.hora_inicio,
                r.hora_fin,
                r.modalidad,
                r.lugar_enlace,
                r.tema,
                r.acuerdos,
                r.observaciones,
                r.estado,
                r.asistencia_tutor,
                r.asistencia_estudiante,
                r.estado_validacion,
                r.observacion_validacion,
                r.fecha_validacion,
                r.fecha_registro

            ORDER BY
                r.fecha_reunion DESC,
                r.hora_inicio DESC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Busca una reunión comprobando que pertenezca al tutor.
     */
    public function buscarPorIdParaTutor(
        int $idReunion,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                r.*,
                a.id_expediente,

                x.titulo_trabajo,
                x.etapa_actual,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante

            FROM reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            WHERE r.id_reunion = :id_reunion
                AND t.id_usuario = :id_usuario

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_reunion' => $idReunion,
            'id_usuario' => $idUsuario
        ]);

        $reunion = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $reunion ?: null;
    }

    /**
     * Registra una reunión para una asignación aceptada y vigente.
     */
    public function crear(
        int $idExpediente,
        int $idUsuario,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        string $modalidad,
        ?string $lugarEnlace,
        string $tema,
        ?string $acuerdos,
        ?string $observaciones
    ): int {
        try {
            $this->conexion->beginTransaction();

            // Comprobamos nuevamente la asignación dentro de la transacción.
            $sqlAsignacion = "
                SELECT a.id_asignacion
                FROM asignaciones_tutor AS a

                INNER JOIN tutores AS t
                    ON t.id_tutor = a.id_tutor

                INNER JOIN cartas_designacion_mg AS c
                    ON c.id_asignacion = a.id_asignacion
                    AND c.estado = 'aceptada'

                INNER JOIN expedientes_mg AS x
                    ON x.id_expediente = a.id_expediente

                WHERE a.id_expediente = :id_expediente
                    AND t.id_usuario = :id_usuario
                    AND a.estado = 'vigente'
                    AND a.asignacion_vigente = 1
                    AND x.estado = 'activo'

                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaAsignacion = $this->conexion->prepare(
                $sqlAsignacion
            );

            $sentenciaAsignacion->execute([
                'id_expediente' => $idExpediente,
                'id_usuario' => $idUsuario
            ]);

            $idAsignacion = $sentenciaAsignacion->fetchColumn();

            if (!$idAsignacion) {
                throw new RuntimeException(
                    'No tienes una asignación aceptada y vigente para este expediente.'
                );
            }

            $sql = "
                INSERT INTO reuniones_mg (
                    id_asignacion,
                    fecha_reunion,
                    hora_inicio,
                    hora_fin,
                    modalidad,
                    lugar_enlace,
                    tema,
                    acuerdos,
                    observaciones,
                    registrado_por
                )
                VALUES (
                    :id_asignacion,
                    :fecha_reunion,
                    :hora_inicio,
                    :hora_fin,
                    :modalidad,
                    :lugar_enlace,
                    :tema,
                    :acuerdos,
                    :observaciones,
                    :registrado_por
                )
            ";

            $sentencia = $this->conexion->prepare($sql);

            $sentencia->execute([
                'id_asignacion' => (int) $idAsignacion,
                'fecha_reunion' => $fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'modalidad' => $modalidad,
                'lugar_enlace' => $lugarEnlace,
                'tema' => $tema,
                'acuerdos' => $acuerdos,
                'observaciones' => $observaciones,
                'registrado_por' => $idUsuario
            ]);

            $idReunion = (int) $this->conexion
                ->lastInsertId();

            $this->conexion->commit();

            return $idReunion;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Registra el resultado y la asistencia de una reunión.
     */
    public function registrarResultado(
        int $idReunion,
        int $idUsuario,
        string $asistenciaTutor,
        string $asistenciaEstudiante,
        ?string $acuerdos,
        ?string $observaciones
    ): bool {
        $sql = "
            UPDATE reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            SET
                r.estado = 'realizada',
                r.asistencia_tutor = :asistencia_tutor,
                r.asistencia_estudiante = :asistencia_estudiante,
                r.acuerdos = :acuerdos,
                r.observaciones = :observaciones,
                r.estado_validacion = 'pendiente',
                r.observacion_validacion = NULL,
                r.validada_por = NULL,
                r.fecha_validacion = NULL

            WHERE r.id_reunion = :id_reunion
                AND t.id_usuario = :id_usuario
                AND r.estado != 'cancelada'
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'asistencia_tutor' => $asistenciaTutor,
            'asistencia_estudiante' => $asistenciaEstudiante,
            'acuerdos' => $acuerdos,
            'observaciones' => $observaciones,
            'id_reunion' => $idReunion,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->rowCount() === 1;
    }

    /**
     * Cancela una reunión programada sin eliminarla.
     */
    public function cancelar(
        int $idReunion,
        int $idUsuario,
        string $motivo
    ): bool {
        $sql = "
            UPDATE reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            SET
                r.estado = 'cancelada',
                r.observaciones = :motivo

            WHERE r.id_reunion = :id_reunion
                AND t.id_usuario = :id_usuario
                AND r.estado = 'programada'
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'motivo' => $motivo,
            'id_reunion' => $idReunion,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->rowCount() === 1;
    }
}