<?php

class ParametroMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los parámetros registrados.
     */
    public function listar(string $busqueda = ''): array
    {
        $sql = "
            SELECT
                p.clave,
                p.valor,
                p.descripcion,
                p.fuente,
                p.estado_evidencia,
                p.actualizado_por,
                p.fecha_actualizacion,
                u.nombre AS nombre_actualizador,
                u.apellido AS apellido_actualizador
            FROM parametros_mg AS p
            LEFT JOIN usuarios AS u
                ON u.id_usuario = p.actualizado_por
            WHERE (
                p.clave LIKE :buscar_clave
                OR p.descripcion LIKE :buscar_descripcion
                OR p.fuente LIKE :buscar_fuente
            )
            ORDER BY p.clave ASC
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'buscar_clave' => $patronBusqueda,
            'buscar_descripcion' => $patronBusqueda,
            'buscar_fuente' => $patronBusqueda
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Busca un parámetro mediante su clave.
     */
    public function buscarPorClave(
        string $clave
    ): ?array {
        $sql = "
            SELECT
                clave,
                valor,
                descripcion,
                fuente,
                estado_evidencia,
                actualizado_por,
                fecha_actualizacion
            FROM parametros_mg
            WHERE clave = :clave
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'clave' => trim($clave)
        ]);

        $parametro = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $parametro ?: null;
    }

    /**
     * Devuelve únicamente el valor de un parámetro.
     */
    public function obtenerValor(
        string $clave
    ): ?string {
        $sql = "
            SELECT valor
            FROM parametros_mg
            WHERE clave = :clave
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'clave' => trim($clave)
        ]);

        $valor = $sentencia->fetchColumn();

        return $valor !== false
            ? (string) $valor
            : null;
    }

    /**
     * Actualiza un parámetro existente.
     */
    public function actualizar(
        string $clave,
        ?string $valor,
        string $descripcion,
        string $fuente,
        string $estadoEvidencia,
        int $idUsuario
    ): bool {
        $sql = "
            UPDATE parametros_mg
            SET
                valor = :valor,
                descripcion = :descripcion,
                fuente = :fuente,
                estado_evidencia = :estado_evidencia,
                actualizado_por = :actualizado_por
            WHERE clave = :clave
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'valor' => $valor,
            'descripcion' => trim($descripcion),
            'fuente' => trim($fuente),
            'estado_evidencia' => $estadoEvidencia,
            'actualizado_por' => $idUsuario,
            'clave' => trim($clave)
        ]);
    }
}