<?php

class CalendarioMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los hitos junto con la cohorte correspondiente.
     */
    public function listar(
        string $busqueda = '',
        ?int $idCohorte = null,
        ?string $etapa = null
    ): array {
        $sql = "
            SELECT
                h.id_hito,
                h.id_cohorte,
                h.etapa,
                h.tipo,
                h.nombre,
                h.orden,
                h.fecha_limite,
                h.avance_esperado_pct,
                h.fecha_registro,
                c.codigo AS codigo_cohorte,
                c.nombre AS nombre_cohorte,
                c.activa AS cohorte_activa
            FROM calendario_mg AS h
            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = h.id_cohorte
            WHERE (
                h.nombre LIKE :buscar_nombre
                OR h.tipo LIKE :buscar_tipo
                OR c.codigo LIKE :buscar_cohorte
            )
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $parametros = [
            'buscar_nombre' => $patronBusqueda,
            'buscar_tipo' => $patronBusqueda,
            'buscar_cohorte' => $patronBusqueda
        ];

        if ($idCohorte !== null) {
            $sql .= "
                AND h.id_cohorte = :id_cohorte
            ";

            $parametros['id_cohorte'] = $idCohorte;
        }

        if ($etapa !== null) {
            $sql .= "
                AND h.etapa = :etapa
            ";

            $parametros['etapa'] = $etapa;
        }

        $sql .= "
            ORDER BY
                c.fecha_inicio DESC,
                CASE h.etapa
                    WHEN 'previa' THEN 1
                    WHEN 'mg1' THEN 2
                    WHEN 'mg2' THEN 3
                    ELSE 4
                END,
                h.orden ASC,
                h.fecha_limite ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Busca un hito por su identificador.
     */
    public function buscarPorId(
        int $idHito
    ): ?array {
        $sql = "
            SELECT
                id_hito,
                id_cohorte,
                etapa,
                tipo,
                nombre,
                orden,
                fecha_limite,
                avance_esperado_pct,
                fecha_registro
            FROM calendario_mg
            WHERE id_hito = :id_hito
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_hito' => $idHito
        ]);

        $hito = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $hito ?: null;
    }

    /**
     * Comprueba si el orden ya se utiliza en la misma etapa.
     */
    public function existeOrden(
        int $idCohorte,
        string $etapa,
        int $orden,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM calendario_mg
            WHERE id_cohorte = :id_cohorte
                AND etapa = :etapa
                AND orden = :orden
        ";

        $parametros = [
            'id_cohorte' => $idCohorte,
            'etapa' => $etapa,
            'orden' => $orden
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_hito != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Registra un hito en el calendario.
     */
    public function crear(
        int $idCohorte,
        string $etapa,
        string $tipo,
        string $nombre,
        int $orden,
        string $fechaLimite,
        ?int $avanceEsperado
    ): bool {
        $sql = "
            INSERT INTO calendario_mg (
                id_cohorte,
                etapa,
                tipo,
                nombre,
                orden,
                fecha_limite,
                avance_esperado_pct
            )
            VALUES (
                :id_cohorte,
                :etapa,
                :tipo,
                :nombre,
                :orden,
                :fecha_limite,
                :avance_esperado_pct
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_cohorte' => $idCohorte,
            'etapa' => $etapa,
            'tipo' => $tipo,
            'nombre' => trim($nombre),
            'orden' => $orden,
            'fecha_limite' => $fechaLimite,
            'avance_esperado_pct' => $avanceEsperado
        ]);
    }

    /**
     * Actualiza un hito existente.
     */
    public function actualizar(
        int $idHito,
        int $idCohorte,
        string $etapa,
        string $tipo,
        string $nombre,
        int $orden,
        string $fechaLimite,
        ?int $avanceEsperado
    ): bool {
        $sql = "
            UPDATE calendario_mg
            SET
                id_cohorte = :id_cohorte,
                etapa = :etapa,
                tipo = :tipo,
                nombre = :nombre,
                orden = :orden,
                fecha_limite = :fecha_limite,
                avance_esperado_pct = :avance_esperado_pct
            WHERE id_hito = :id_hito
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_cohorte' => $idCohorte,
            'etapa' => $etapa,
            'tipo' => $tipo,
            'nombre' => trim($nombre),
            'orden' => $orden,
            'fecha_limite' => $fechaLimite,
            'avance_esperado_pct' => $avanceEsperado,
            'id_hito' => $idHito
        ]);
    }
}