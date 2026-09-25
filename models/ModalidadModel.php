<?php

class ModalidadModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las modalidades oficiales.
     */
    public function listar(string $busqueda = ''): array
    {
        $sql = "
            SELECT
                id_modalidad,
                codigo,
                nombre,
                descripcion,
                requiere_tutor,
                flujo,
                activa,
                fecha_registro
            FROM modalidades_grado
            WHERE (
                codigo LIKE :buscar_codigo
                OR nombre LIKE :buscar_nombre
                OR COALESCE(
                    descripcion,
                    ''
                ) LIKE :buscar_descripcion
            )
            ORDER BY nombre ASC
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'buscar_codigo' => $patronBusqueda,
            'buscar_nombre' => $patronBusqueda,
            'buscar_descripcion' => $patronBusqueda
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene una modalidad por su identificador.
     */
    public function buscarPorId(
        int $idModalidad
    ): ?array {
        $sql = "
            SELECT
                id_modalidad,
                codigo,
                nombre,
                descripcion,
                requiere_tutor,
                flujo,
                activa,
                fecha_registro
            FROM modalidades_grado
            WHERE id_modalidad = :id_modalidad
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_modalidad' => $idModalidad
        ]);

        $modalidad = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $modalidad ?: null;
    }

    /**
     * Obtiene solamente modalidades activas.
     */
    public function listarActivas(): array
    {
        $sql = "
            SELECT
                id_modalidad,
                codigo,
                nombre,
                descripcion,
                requiere_tutor,
                flujo
            FROM modalidades_grado
            WHERE activa = 1
            ORDER BY nombre ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Comprueba que el nombre no esté repetido.
     */
    public function existeNombre(
        string $nombre,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM modalidades_grado
            WHERE nombre = :nombre
        ";

        $parametros = [
            'nombre' => trim($nombre)
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_modalidad != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Comprueba que el código no esté repetido.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM modalidades_grado
            WHERE codigo = :codigo
        ";

        $parametros = [
            'codigo' => strtoupper(
                trim($codigo)
            )
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_modalidad != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Registra una modalidad.
     */
    public function crear(
        string $codigo,
        string $nombre,
        ?string $descripcion,
        bool $requiereTutor,
        string $flujo
    ): bool {
        $sql = "
            INSERT INTO modalidades_grado (
                codigo,
                nombre,
                descripcion,
                requiere_tutor,
                flujo,
                activa
            )
            VALUES (
                :codigo,
                :nombre,
                :descripcion,
                :requiere_tutor,
                :flujo,
                1
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => strtoupper(
                trim($codigo)
            ),
            'nombre' => trim($nombre),
            'descripcion' => $descripcion,
            'requiere_tutor' => $requiereTutor
                ? 1
                : 0,
            'flujo' => $flujo
        ]);
    }

    /**
     * Actualiza la información de una modalidad.
     */
    public function actualizar(
        int $idModalidad,
        string $codigo,
        string $nombre,
        ?string $descripcion,
        bool $requiereTutor,
        string $flujo
    ): bool {
        $sql = "
            UPDATE modalidades_grado
            SET
                codigo = :codigo,
                nombre = :nombre,
                descripcion = :descripcion,
                requiere_tutor = :requiere_tutor,
                flujo = :flujo
            WHERE id_modalidad = :id_modalidad
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'codigo' => strtoupper(
                trim($codigo)
            ),
            'nombre' => trim($nombre),
            'descripcion' => $descripcion,
            'requiere_tutor' => $requiereTutor
                ? 1
                : 0,
            'flujo' => $flujo,
            'id_modalidad' => $idModalidad
        ]);
    }

    /**
     * Activa o desactiva una modalidad.
     */
    public function cambiarEstado(
        int $idModalidad,
        bool $activa
    ): bool {
        $sql = "
            UPDATE modalidades_grado
            SET activa = :activa
            WHERE id_modalidad = :id_modalidad
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'activa' => $activa ? 1 : 0,
            'id_modalidad' => $idModalidad
        ]);
    }

    /**
     * Indica si la modalidad necesita un tutor.
     */
    public function requiereTutor(
        int $idModalidad
    ): bool {
        $sql = "
            SELECT requiere_tutor
            FROM modalidades_grado
            WHERE id_modalidad = :id_modalidad
                AND activa = 1
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_modalidad' => $idModalidad
        ]);

        return (int) $sentencia->fetchColumn() === 1;
    }
}