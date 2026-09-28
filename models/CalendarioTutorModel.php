<?php

class CalendarioTutorModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los hitos de las cohortes asignadas al tutor.
     */
    public function listarPorTutor(
        int $idUsuario
    ): array {
        $sql = "
            SELECT DISTINCT
                h.id_hito,
                h.etapa,
                h.tipo,
                h.nombre,
                h.orden,
                h.fecha_limite,
                h.avance_esperado_pct,

                ch.id_cohorte,
                ch.nombre AS nombre_cohorte,

                x.id_expediente,
                x.titulo_trabajo,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante

            FROM calendario_mg AS h

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = h.id_cohorte

            INNER JOIN expedientes_mg AS x
                ON x.id_cohorte = ch.id_cohorte

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN asignaciones_tutor AS a
                ON a.id_expediente = x.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN cartas_designacion_mg AS cd
                ON cd.id_asignacion = a.id_asignacion
                AND cd.estado = 'aceptada'

            WHERE t.id_usuario = :id_usuario
                AND x.estado = 'activo'

            ORDER BY
                h.fecha_limite ASC,
                h.orden ASC,
                ue.nombre ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}