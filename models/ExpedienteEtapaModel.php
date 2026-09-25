<?php

class ExpedienteEtapaModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista el historial de etapas de un expediente.
     */
    public function listarPorExpediente(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                ee.id_expediente_etapa,
                ee.id_expediente,
                ee.etapa,
                ee.fecha_inicio,
                ee.fecha_fin,
                ee.resultado,
                ee.motivo_cierre,
                ee.registrado_por,
                ee.fecha_registro,

                u.nombre AS nombre_registrador,
                u.apellido AS apellido_registrador

            FROM expediente_etapas AS ee

            INNER JOIN usuarios AS u
                ON u.id_usuario = ee.registrado_por

            WHERE ee.id_expediente = :id_expediente

            ORDER BY
                ee.fecha_inicio ASC,
                ee.id_expediente_etapa ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene la etapa que todavía se encuentra abierta.
     */
    public function buscarEtapaAbierta(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                id_expediente_etapa,
                id_expediente,
                etapa,
                fecha_inicio,
                fecha_fin,
                resultado,
                motivo_cierre,
                registrado_por,
                fecha_registro
            FROM expediente_etapas
            WHERE id_expediente = :id_expediente
                AND fecha_fin IS NULL
            ORDER BY id_expediente_etapa DESC
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente
        ]);

        $etapa = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $etapa ?: null;
    }
}