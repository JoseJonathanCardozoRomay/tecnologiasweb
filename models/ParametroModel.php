<?php

class ParametroModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los parámetros registrados en el sistema.
     */
    public function listar(
        string $busqueda = '',
        ?string $estadoEvidencia = null
    ): array {
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
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $parametros = [
            'buscar_clave' => $patronBusqueda,
            'buscar_descripcion' => $patronBusqueda,
            'buscar_fuente' => $patronBusqueda
        ];

        if ($estadoEvidencia !== null) {
            $sql .= "
                AND p.estado_evidencia = :estado_evidencia
            ";

            $parametros['estado_evidencia'] = $estadoEvidencia;
        }

        $sql .= "
            ORDER BY
                p.estado_evidencia ASC,
                p.clave ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

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
            WHERE p.clave = :clave
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
     * Actualiza el valor de un parámetro y registra al responsable.
     */
    public function actualizarValor(
        string $clave,
        ?string $valor,
        int $idUsuario
    ): bool {
        $sql = "
            UPDATE parametros_mg
            SET
                valor = :valor,
                actualizado_por = :actualizado_por
            WHERE clave = :clave
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'valor' => $valor,
            'actualizado_por' => $idUsuario,
            'clave' => trim($clave)
        ]);
    }
}