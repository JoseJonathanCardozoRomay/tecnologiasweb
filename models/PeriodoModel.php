<?php

class PeriodoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los periodos con información resumida.
     */
    public function listar(string $busqueda = ''): array
    {
        $sql = "
            SELECT
                p.id_periodo,
                p.codigo,
                p.nombre,
                p.fecha_inicio,
                p.fecha_fin,
                p.estado,
                p.fecha_registro,
                COUNT(
                    DISTINCT tp.id_tutor_periodo
                ) AS tutores_configurados,
                COUNT(
                    DISTINCT t.id_tutoria
                ) AS procesos_registrados

            FROM periodos_inscripcion AS p

            LEFT JOIN tutor_periodo AS tp
                ON tp.id_periodo = p.id_periodo

            LEFT JOIN tutorias AS t
                ON t.id_periodo = p.id_periodo

            WHERE
                p.codigo LIKE :buscar_codigo
                OR p.nombre LIKE :buscar_nombre

            GROUP BY
                p.id_periodo,
                p.codigo,
                p.nombre,
                p.fecha_inicio,
                p.fecha_fin,
                p.estado,
                p.fecha_registro

            ORDER BY
                p.fecha_inicio DESC,
                p.id_periodo DESC
        ";

        $patronBusqueda = '%' . trim($busqueda) . '%';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'buscar_codigo' => $patronBusqueda,
            'buscar_nombre' => $patronBusqueda
        ]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un periodo por su identificador.
     */
    public function buscarPorId(int $idPeriodo): ?array
    {
        $sql = "
            SELECT
                id_periodo,
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin,
                estado,
                fecha_registro
            FROM periodos_inscripcion
            WHERE id_periodo = :id_periodo
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_periodo' => $idPeriodo
        ]);

        $periodo = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $periodo ?: null;
    }

    /**
     * Comprueba que el código no se encuentre repetido.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM periodos_inscripcion
            WHERE codigo = :codigo
        ";

        $parametros = [
            'codigo' => $codigo
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_periodo != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Registra un nuevo periodo académico.
     */
    public function crear(
        string $codigo,
        string $nombre,
        string $fechaInicio,
        string $fechaFin,
        string $estado
    ): bool {
        $sql = "
            INSERT INTO periodos_inscripcion (
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin,
                estado
            )
            VALUES (
                :codigo,
                :nombre,
                :fecha_inicio,
                :fecha_fin,
                :estado
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $estado
        ]);
    }

    /**
     * Actualiza los datos de un periodo académico.
     */
    public function actualizar(
        int $idPeriodo,
        string $codigo,
        string $nombre,
        string $fechaInicio,
        string $fechaFin,
        string $estado
    ): bool {
        $sql = "
            UPDATE periodos_inscripcion
            SET
                codigo = :codigo,
                nombre = :nombre,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                estado = :estado
            WHERE id_periodo = :id_periodo
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $estado,
            'id_periodo' => $idPeriodo
        ]);
    }

    /**
     * Cambia únicamente el estado del periodo.
     */
    public function cambiarEstado(
        int $idPeriodo,
        string $estado
    ): bool {
        $sql = "
            UPDATE periodos_inscripcion
            SET estado = :estado
            WHERE id_periodo = :id_periodo
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'estado' => $estado,
            'id_periodo' => $idPeriodo
        ]);
    }
}