<?php

class PanelCoordinacionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los indicadores principales del proceso de grado.
     */
    public function obtenerResumen(): array
    {
        $sql = "
            SELECT
                (
                    SELECT COUNT(*)
                    FROM expedientes_mg
                    WHERE estado = 'activo'
                ) AS expedientes_activos,

                (
                    SELECT COUNT(*)
                    FROM expedientes_mg AS e
                    WHERE e.estado = 'activo'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM asignaciones_tutor AS a
                        WHERE a.id_expediente = e.id_expediente
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                    )
                ) AS expedientes_sin_tutor,

                (
                    SELECT COUNT(*)
                    FROM cartas_designacion_mg
                    WHERE estado = 'pendiente'
                ) AS cartas_pendientes,

                (
                    SELECT COUNT(*)
                    FROM reuniones_mg
                    WHERE estado_validacion = 'pendiente'
                    AND estado = 'realizada'
                ) AS reuniones_pendientes,

                (
                    SELECT COUNT(*)
                    FROM informes_avance_mg
                    WHERE estado = 'presentado'
                ) AS informes_pendientes,

                (
                    SELECT COUNT(*)
                    FROM defensas_mg
                    WHERE estado = 'programada'
                    AND defensa_vigente = 1
                ) AS defensas_programadas
        ";

        $sentencia = $this->conexion->query($sql);
        $resumen = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $resumen ?: [
            'expedientes_activos' => 0,
            'expedientes_sin_tutor' => 0,
            'cartas_pendientes' => 0,
            'reuniones_pendientes' => 0,
            'informes_pendientes' => 0,
            'defensas_programadas' => 0
        ];
    }

    /**
     * Lista las próximas defensas programadas.
     */
    public function listarProximasDefensas(
        int $limite = 5
    ): array {
        $limite = max(
            1,
            min($limite, 20)
        );

        $sql = "
            SELECT
                d.id_defensa,
                d.id_expediente,
                d.etapa,
                d.fecha,
                d.hora_inicio,
                d.hora_fin,
                d.ambiente,
                e.titulo_trabajo,
                u.nombre,
                u.apellido
            FROM defensas_mg AS d
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            WHERE d.estado = 'programada'
                AND d.defensa_vigente = 1
                AND d.fecha >= CURRENT_DATE
            ORDER BY
                d.fecha ASC,
                d.hora_inicio ASC
            LIMIT {$limite}
        ";

        $sentencia = $this->conexion->query($sql);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Lista los expedientes modificados recientemente.
     */
    public function listarExpedientesRecientes(
        int $limite = 5
    ): array {
        $limite = max(
            1,
            min($limite, 20)
        );

        $sql = "
            SELECT
                e.id_expediente,
                e.etapa_actual,
                e.estado,
                e.titulo_trabajo,
                e.fecha_registro,
                u.nombre,
                u.apellido,
                m.nombre AS modalidad,
                c.nombre AS cohorte
            FROM expedientes_mg AS e
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad
            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte
            ORDER BY e.fecha_registro DESC
            LIMIT {$limite}
        ";

        $sentencia = $this->conexion->query($sql);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}