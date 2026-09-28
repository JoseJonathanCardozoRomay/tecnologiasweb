<?php

class DocumentoAccesoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Comprueba que el documento corresponda al estudiante.
     */
    public function perteneceAEstudiante(
        int $idDocumento,
        int $idUsuario
    ): bool {
        $sql = "
            SELECT COUNT(*)

            FROM documentos_generados AS d

            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            WHERE d.id_documento = :id_documento
                AND es.id_usuario = :id_usuario
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_documento' => $idDocumento,
            'id_usuario' => $idUsuario
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Comprueba que el tutor participe en el expediente como tutor
     * principal o como tribunal de la defensa.
     */
    public function perteneceATutor(
        int $idDocumento,
        int $idUsuario
    ): bool {
        $sql = "
            SELECT COUNT(*)

            FROM documentos_generados AS d

            INNER JOIN tutores AS t
                ON t.id_usuario = :id_usuario

            WHERE d.id_documento = :id_documento
                AND (
                    EXISTS (
                        SELECT 1
                        FROM asignaciones_tutor AS a
                        WHERE a.id_expediente = d.id_expediente
                            AND a.id_tutor = t.id_tutor
                            AND (
                                d.tipo <> 'carta_tutor'
                                OR EXISTS (
                                    SELECT 1 FROM cartas_designacion_mg AS c
                                    WHERE c.id_asignacion = a.id_asignacion
                                        AND c.numero_carta = d.numero_correlativo
                                )
                            )
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM defensas_mg AS df
                        INNER JOIN tribunales_defensa AS td
                            ON td.id_expediente
                                = df.id_expediente
                            AND td.etapa = df.etapa
                        WHERE df.id_defensa = d.id_defensa
                            AND td.id_tutor = t.id_tutor
                            AND td.estado = 'vigente'
                    )
                )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_documento' => $idDocumento,
            'id_usuario' => $idUsuario
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }
}
