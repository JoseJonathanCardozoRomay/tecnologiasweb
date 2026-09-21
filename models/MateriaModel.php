<?php

class MateriaModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Listamos las materias con su carrera correspondiente
    public function listar(
        string $busqueda = '',
        ?int $idCarrera = null
    ): array {
        $sql = "
            SELECT
                m.id_materia,
                m.nombre_materia,
                m.id_carrera,
                c.nombre_carrera
            FROM materias AS m
            LEFT JOIN carreras AS c
                ON m.id_carrera = c.id_carrera
            WHERE m.nombre_materia LIKE :busqueda
        ";

        $parametros = [
            'busqueda' => '%' . $busqueda . '%'
        ];

        if ($idCarrera !== null) {
            $sql .= " AND m.id_carrera = :id_carrera";
            $parametros['id_carrera'] = $idCarrera;
        }

        $sql .= " ORDER BY m.nombre_materia ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    // Buscamos una materia mediante su identificador
    public function buscarPorId(int $idMateria): ?array
    {
        $sql = "
            SELECT
                id_materia,
                nombre_materia,
                id_carrera
            FROM materias
            WHERE id_materia = :id_materia
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_materia' => $idMateria
        ]);

        $resultado = $consulta->fetch();

        return $resultado ?: null;
    }

    // Evitamos repetir una materia dentro de la misma carrera
    public function existeNombre(
        string $nombreMateria,
        ?int $idCarrera,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM materias
            WHERE nombre_materia = :nombre_materia
        ";

        $parametros = [
            'nombre_materia' => $nombreMateria
        ];

        if ($idCarrera === null) {
            $sql .= " AND id_carrera IS NULL";
        } else {
            $sql .= " AND id_carrera = :id_carrera";
            $parametros['id_carrera'] = $idCarrera;
        }

        if ($idExcluir !== null) {
            $sql .= " AND id_materia != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Registramos una materia nueva
    public function crear(
        string $nombreMateria,
        ?int $idCarrera
    ): bool {
        $sql = "
            INSERT INTO materias (
                nombre_materia,
                id_carrera
            )
            VALUES (
                :nombre_materia,
                :id_carrera
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'nombre_materia' => $nombreMateria,
            'id_carrera' => $idCarrera
        ]);
    }

    // Actualizamos los datos de una materia
    public function actualizar(
        int $idMateria,
        string $nombreMateria,
        ?int $idCarrera
    ): bool {
        $sql = "
            UPDATE materias
            SET
                nombre_materia = :nombre_materia,
                id_carrera = :id_carrera
            WHERE id_materia = :id_materia
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'nombre_materia' => $nombreMateria,
            'id_carrera' => $idCarrera,
            'id_materia' => $idMateria
        ]);
    }

    // Revisamos si la materia está asignada o tiene tutorías
    public function tieneRegistrosRelacionados(int $idMateria): bool
    {
        $sql = "
            SELECT (
                SELECT COUNT(*)
                FROM tutor_materia
                WHERE id_materia = :id_asignaciones
            ) + (
                SELECT COUNT(*)
                FROM tutorias
                WHERE id_materia = :id_tutorias
            ) AS total
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_asignaciones' => $idMateria,
            'id_tutorias' => $idMateria
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Eliminamos una materia sin registros relacionados
    public function eliminar(int $idMateria): bool
    {
        $sql = "
            DELETE FROM materias
            WHERE id_materia = :id_materia
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_materia' => $idMateria
        ]);
    }
}