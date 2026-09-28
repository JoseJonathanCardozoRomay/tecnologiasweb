<?php

class EvidenciaReunionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las evidencias comprobando que la reunión sea del tutor.
     */
    public function listarPorReunion(
        int $idReunion,
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                ev.id_evidencia,
                ev.id_reunion,
                ev.tipo,
                ev.nombre_original,
                ev.ruta_archivo,
                ev.tipo_mime,
                ev.tamano_bytes,
                ev.hash_archivo,
                ev.descripcion,
                ev.fecha_registro

            FROM evidencias_reunion_mg AS ev

            INNER JOIN reuniones_mg AS r
                ON r.id_reunion = ev.id_reunion

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            WHERE ev.id_reunion = :id_reunion
                AND t.id_usuario = :id_usuario

            ORDER BY ev.fecha_registro DESC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_reunion' => $idReunion,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Busca una evidencia y verifica que pertenezca al tutor.
     */
    public function buscarPorIdParaTutor(
        int $idEvidencia,
        int $idUsuario
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

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            WHERE ev.id_evidencia = :id_evidencia
                AND t.id_usuario = :id_usuario

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_evidencia' => $idEvidencia,
            'id_usuario' => $idUsuario
        ]);

        $evidencia = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $evidencia ?: null;
    }

    /**
     * Registra los datos del archivo después de guardarlo.
     */
    public function crear(
        int $idReunion,
        string $tipo,
        string $nombreOriginal,
        string $nombreGuardado,
        string $tipoMime,
        int $tamanoBytes,
        string $hashArchivo,
        ?string $descripcion,
        int $idUsuario
    ): int {
        $sql = "
            INSERT INTO evidencias_reunion_mg (
                id_reunion,
                tipo,
                nombre_original,
                ruta_archivo,
                tipo_mime,
                tamano_bytes,
                hash_archivo,
                descripcion,
                subido_por
            )
            VALUES (
                :id_reunion,
                :tipo,
                :nombre_original,
                :ruta_archivo,
                :tipo_mime,
                :tamano_bytes,
                :hash_archivo,
                :descripcion,
                :subido_por
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_reunion' => $idReunion,
            'tipo' => $tipo,
            'nombre_original' => $nombreOriginal,
            'ruta_archivo' => $nombreGuardado,
            'tipo_mime' => $tipoMime,
            'tamano_bytes' => $tamanoBytes,
            'hash_archivo' => $hashArchivo,
            'descripcion' => $descripcion,
            'subido_por' => $idUsuario
        ]);

        return (int) $this->conexion
            ->lastInsertId();
    }
}