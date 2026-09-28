<?php

class RevisionSeguimientoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los informes pendientes de revisión.
     */
    public function listarInformesPendientes(): array
    {
        $sql = "
            SELECT
                i.id_informe,
                i.etapa,
                i.numero_informe,
                i.fecha_informe,
                i.porcentaje_avance,
                i.estado,
                i.fecha_actualizacion,

                x.id_expediente,
                x.titulo_trabajo,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,

                m.nombre AS nombre_modalidad,
                ch.nombre AS nombre_cohorte,

                ur.nombre AS nombre_tutor,
                ur.apellido AS apellido_tutor

            FROM informes_avance_mg AS i

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = i.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            INNER JOIN usuarios AS ur
                ON ur.id_usuario = i.registrado_por

            WHERE i.estado = 'presentado'

            ORDER BY
                i.fecha_actualizacion ASC,
                i.id_informe ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene un informe para su revisión.
     */
    public function buscarInforme(
        int $idInforme
    ): ?array {
        $sql = "
            SELECT
                i.*,

                x.titulo_trabajo,
                x.etapa_actual,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,
                ue.correo AS correo_estudiante,

                m.nombre AS nombre_modalidad,
                ch.nombre AS nombre_cohorte,

                h.nombre AS nombre_hito,
                h.fecha_limite,

                ur.nombre AS nombre_tutor,
                ur.apellido AS apellido_tutor

            FROM informes_avance_mg AS i

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = i.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            INNER JOIN usuarios AS ur
                ON ur.id_usuario = i.registrado_por

            LEFT JOIN calendario_mg AS h
                ON h.id_hito = i.id_hito

            WHERE i.id_informe = :id_informe
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_informe' => $idInforme
        ]);

        $informe = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $informe ?: null;
    }

    /**
     * Aprueba u observa un informe presentado.
     */
    public function revisarInforme(
        int $idInforme,
        int $idUsuario,
        string $decision,
        ?string $observacion
    ): bool {
        if (
            !in_array(
                $decision,
                ['aprobado', 'observado'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'La decisión seleccionada no es válida.'
            );
        }

        if (
            $decision === 'observado'
            && (
                $observacion === null
                || trim($observacion) === ''
            )
        ) {
            throw new InvalidArgumentException(
                'Debes explicar la observación del informe.'
            );
        }

        try {
            $this->conexion->beginTransaction();

            $sqlBloqueo = "
                SELECT estado
                FROM informes_avance_mg
                WHERE id_informe = :id_informe
                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaBloqueo = $this->conexion->prepare(
                $sqlBloqueo
            );

            $sentenciaBloqueo->execute([
                'id_informe' => $idInforme
            ]);

            $estadoActual = $sentenciaBloqueo->fetchColumn();

            if ($estadoActual === false) {
                throw new RuntimeException(
                    'El informe solicitado no existe.'
                );
            }

            if ($estadoActual !== 'presentado') {
                throw new RuntimeException(
                    'El informe ya fue revisado o modificado.'
                );
            }

            $sql = "
                UPDATE informes_avance_mg
                SET
                    estado = :estado,
                    observacion_revision = :observacion,
                    revisado_por = :revisado_por,
                    fecha_revision = NOW()
                WHERE id_informe = :id_informe
            ";

            $sentencia = $this->conexion->prepare($sql);

            $resultado = $sentencia->execute([
                'estado' => $decision,
                'observacion' => $decision === 'observado'
                    ? trim((string) $observacion)
                    : null,
                'revisado_por' => $idUsuario,
                'id_informe' => $idInforme
            ]);

            $this->conexion->commit();

            return $resultado;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Lista las reuniones realizadas pendientes de validación.
     */
    public function listarReunionesPendientes(): array
    {
        $sql = "
            SELECT
                r.id_reunion,
                r.fecha_reunion,
                r.hora_inicio,
                r.tema,
                r.modalidad,
                r.asistencia_tutor,
                r.asistencia_estudiante,
                r.estado_validacion,

                a.id_expediente,

                x.titulo_trabajo,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,

                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor,

                COUNT(ev.id_evidencia) AS total_evidencias

            FROM reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            LEFT JOIN evidencias_reunion_mg AS ev
                ON ev.id_reunion = r.id_reunion

            WHERE r.estado = 'realizada'
                AND r.estado_validacion = 'pendiente'

            GROUP BY
                r.id_reunion,
                r.fecha_reunion,
                r.hora_inicio,
                r.tema,
                r.modalidad,
                r.asistencia_tutor,
                r.asistencia_estudiante,
                r.estado_validacion,
                a.id_expediente,
                x.titulo_trabajo,
                ue.nombre,
                ue.apellido,
                ut.nombre,
                ut.apellido

            ORDER BY
                r.fecha_reunion ASC,
                r.hora_inicio ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene una reunión para su validación.
     */
    public function buscarReunion(
        int $idReunion
    ): ?array {
        $sql = "
            SELECT
                r.*,
                a.id_expediente,

                x.titulo_trabajo,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,

                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor,

                COUNT(ev.id_evidencia) AS total_evidencias

            FROM reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            LEFT JOIN evidencias_reunion_mg AS ev
                ON ev.id_reunion = r.id_reunion

            WHERE r.id_reunion = :id_reunion

            GROUP BY
                r.id_reunion,
                a.id_expediente,
                x.titulo_trabajo,
                ue.nombre,
                ue.apellido,
                ut.nombre,
                ut.apellido

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_reunion' => $idReunion
        ]);

        $reunion = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $reunion ?: null;
    }

    /**
     * Valida u observa una reunión realizada.
     */
    public function validarReunion(
        int $idReunion,
        int $idUsuario,
        string $decision,
        ?string $observacion
    ): bool {
        if (
            !in_array(
                $decision,
                ['validada', 'observada'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'La decisión seleccionada no es válida.'
            );
        }

        if (
            $decision === 'observada'
            && (
                $observacion === null
                || trim($observacion) === ''
            )
        ) {
            throw new InvalidArgumentException(
                'Debes explicar la observación de la reunión.'
            );
        }

        $sql = "
            UPDATE reuniones_mg
            SET
                estado_validacion = :estado_validacion,
                observacion_validacion = :observacion,
                validada_por = :validada_por,
                fecha_validacion = NOW()
            WHERE id_reunion = :id_reunion
                AND estado = 'realizada'
                AND estado_validacion = 'pendiente'
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'estado_validacion' => $decision,
            'observacion' => $decision === 'observada'
                ? trim((string) $observacion)
                : null,
            'validada_por' => $idUsuario,
            'id_reunion' => $idReunion
        ]);

        return $sentencia->rowCount() === 1;
    }

    /**
 * Lista las evidencias de una reunión para coordinación.
 */
public function listarEvidenciasReunion(
    int $idReunion
): array {
    $sql = "
        SELECT
            id_evidencia,
            tipo,
            nombre_original,
            tipo_mime,
            tamano_bytes,
            descripcion,
            fecha_registro
        FROM evidencias_reunion_mg
        WHERE id_reunion = :id_reunion
        ORDER BY fecha_registro DESC
    ";

    $sentencia = $this->conexion->prepare($sql);

    $sentencia->execute([
        'id_reunion' => $idReunion
    ]);

    return $sentencia->fetchAll(
        PDO::FETCH_ASSOC
    );
}

/**
 * Busca una evidencia para su revisión por coordinación.
 */
public function buscarEvidencia(
    int $idEvidencia
): ?array {
    $sql = "
        SELECT
            ev.*,
            r.id_reunion,
            a.id_expediente

        FROM evidencias_reunion_mg AS ev

        INNER JOIN reuniones_mg AS r
            ON r.id_reunion = ev.id_reunion

        INNER JOIN asignaciones_tutor AS a
            ON a.id_asignacion = r.id_asignacion

        WHERE ev.id_evidencia = :id_evidencia
        LIMIT 1
    ";

    $sentencia = $this->conexion->prepare($sql);

    $sentencia->execute([
        'id_evidencia' => $idEvidencia
    ]);

    $evidencia = $sentencia->fetch(
        PDO::FETCH_ASSOC
    );

    return $evidencia ?: null;
}
}