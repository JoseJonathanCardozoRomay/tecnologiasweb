<?php

class CalificacionEstudianteModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista solamente calificaciones publicadas que pertenecen
     * al estudiante autenticado.
     */
    public function listarPorUsuario(
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                c.id_calificacion,
                c.nota,
                c.observaciones,
                c.fecha_registro,
                c.fecha_actualizacion,

                d.etapa,
                d.fecha AS fecha_defensa,

                e.titulo_trabajo,

                m.nombre AS modalidad,
                co.nombre AS cohorte

            FROM calificaciones_mg AS c

            INNER JOIN defensas_mg AS d
                ON d.id_defensa = c.id_defensa

            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS co
                ON co.id_cohorte = e.id_cohorte

            WHERE es.id_usuario = :id_usuario
                AND c.publicada = 1
                AND d.estado = 'realizada'

            ORDER BY d.fecha DESC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}