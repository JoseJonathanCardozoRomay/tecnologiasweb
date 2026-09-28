<?php

class CalificacionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los límites configurables de calificación.
     */
    public function obtenerLimites(): array
    {
        $sql = "
            SELECT
                clave,
                valor
            FROM parametros_mg
            WHERE clave IN (
                'calificacion_minima',
                'calificacion_maxima'
            )
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $parametros = [];

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $parametro
        ) {
            $parametros[$parametro['clave']]
                = (float) $parametro['valor'];
        }

        return [
            'minimo' => $parametros[
                'calificacion_minima'
            ] ?? 0,
            'maximo' => $parametros[
                'calificacion_maxima'
            ] ?? 100
        ];
    }

    /**
     * Busca la calificación correspondiente a una defensa.
     */
    public function buscarPorDefensa(
        int $idDefensa
    ): ?array {
        $sql = "
            SELECT
                c.id_calificacion,
                c.id_defensa,
                c.nota,
                c.observaciones,
                c.publicada,
                c.registrada_por,
                c.fecha_registro,
                c.fecha_actualizacion,

                d.id_expediente,
                d.etapa,
                d.fecha AS fecha_defensa,
                d.estado AS estado_defensa,

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

            LEFT JOIN calificaciones_mg AS c
                ON c.id_defensa = d.id_defensa

            WHERE d.id_defensa = :id_defensa
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_defensa' => $idDefensa
        ]);

        $resultado = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $resultado ?: null;
    }

    /**
     * Registra o actualiza la calificación de una defensa.
     */
    public function guardar(
        int $idDefensa,
        float $nota,
        ?string $observaciones,
        int $registradoPor
    ): bool {
        $sql = "
            INSERT INTO calificaciones_mg (
                id_defensa,
                nota,
                observaciones,
                publicada,
                registrada_por
            )
            VALUES (
                :id_defensa,
                :nota,
                :observaciones,
                0,
                :registrado_por
            )
            ON DUPLICATE KEY UPDATE
                nota = VALUES(nota),
                observaciones = VALUES(observaciones),
                publicada = 0,
                registrada_por = VALUES(registrada_por)
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_defensa' => $idDefensa,
            'nota' => $nota,
            'observaciones' => $observaciones,
            'registrado_por' => $registradoPor
        ]);
    }

    /**
     * Publica u oculta una calificación.
     */
    public function cambiarPublicacion(
        int $idCalificacion,
        bool $publicada
    ): bool {
        $sql = "
            UPDATE calificaciones_mg
            SET publicada = :publicada
            WHERE id_calificacion = :id_calificacion
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'publicada' => $publicada ? 1 : 0,
            'id_calificacion' => $idCalificacion
        ]);
    }

    /**
     * Comprueba que una calificación pertenezca al estudiante.
     */
    public function buscarPublicadaParaEstudiante(
        int $idCalificacion,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                c.id_calificacion,
                c.nota,
                c.observaciones,
                c.fecha_registro,

                d.etapa,
                d.fecha AS fecha_defensa,

                e.titulo_trabajo

            FROM calificaciones_mg AS c

            INNER JOIN defensas_mg AS d
                ON d.id_defensa = c.id_defensa

            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            WHERE c.id_calificacion = :id_calificacion
                AND es.id_usuario = :id_usuario
                AND c.publicada = 1

            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_calificacion' => $idCalificacion,
            'id_usuario' => $idUsuario
        ]);

        $calificacion = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $calificacion ?: null;
    }
}