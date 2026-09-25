<?php

class CohorteMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las cohortes y permite buscar por código o nombre.
     */
    public function listar(
        string $busqueda = '',
        ?bool $activa = null
    ): array {
        $sql = "
            SELECT
                id_cohorte,
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin,
                activa,
                fecha_registro
            FROM cohortes_mg
            WHERE (
                codigo LIKE :buscar_codigo
                OR nombre LIKE :buscar_nombre
            )
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $parametros = [
            'buscar_codigo' => $patronBusqueda,
            'buscar_nombre' => $patronBusqueda
        ];

        if ($activa !== null) {
            $sql .= "
                AND activa = :activa
            ";

            $parametros['activa'] = $activa
                ? 1
                : 0;
        }

        $sql .= "
            ORDER BY fecha_inicio DESC, nombre ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene una cohorte por su identificador.
     */
    public function buscarPorId(
        int $idCohorte
    ): ?array {
        $sql = "
            SELECT
                id_cohorte,
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin,
                activa,
                fecha_registro
            FROM cohortes_mg
            WHERE id_cohorte = :id_cohorte
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_cohorte' => $idCohorte
        ]);

        $cohorte = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $cohorte ?: null;
    }

    /**
     * Obtiene las cohortes disponibles para otros formularios.
     */
    public function listarActivas(): array
    {
        $sql = "
            SELECT
                id_cohorte,
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin
            FROM cohortes_mg
            WHERE activa = 1
            ORDER BY fecha_inicio DESC, nombre ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Comprueba que el código de la cohorte no esté repetido.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM cohortes_mg
            WHERE codigo = :codigo
        ";

        $parametros = [
            'codigo' => strtoupper(
                trim($codigo)
            )
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_cohorte != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Registra una nueva cohorte.
     */
    public function crear(
        string $codigo,
        string $nombre,
        string $fechaInicio,
        ?string $fechaFin
    ): bool {
        $sql = "
            INSERT INTO cohortes_mg (
                codigo,
                nombre,
                fecha_inicio,
                fecha_fin,
                activa
            )
            VALUES (
                :codigo,
                :nombre,
                :fecha_inicio,
                :fecha_fin,
                1
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => strtoupper(
                trim($codigo)
            ),
            'nombre' => trim($nombre),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
    }

    /**
     * Actualiza una cohorte existente.
     */
    public function actualizar(
        int $idCohorte,
        string $codigo,
        string $nombre,
        string $fechaInicio,
        ?string $fechaFin
    ): bool {
        $sql = "
            UPDATE cohortes_mg
            SET
                codigo = :codigo,
                nombre = :nombre,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin
            WHERE id_cohorte = :id_cohorte
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => strtoupper(
                trim($codigo)
            ),
            'nombre' => trim($nombre),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'id_cohorte' => $idCohorte
        ]);
    }

    /**
     * Activa o desactiva una cohorte.
     */
    public function cambiarEstado(
        int $idCohorte,
        bool $activa
    ): bool {
        $sql = "
            UPDATE cohortes_mg
            SET activa = :activa
            WHERE id_cohorte = :id_cohorte
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'activa' => $activa ? 1 : 0,
            'id_cohorte' => $idCohorte
        ]);
    }
}