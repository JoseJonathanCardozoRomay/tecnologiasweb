<?php

class CarreraModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtenemos las carreras y aplicamos la búsqueda si fue utilizada
    public function listar(string $busqueda = ''): array
    {
        $sql = "
            SELECT
                id_carrera,
                nombre_carrera
            FROM carreras
            WHERE nombre_carrera LIKE :busqueda
            ORDER BY nombre_carrera ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'busqueda' => '%' . $busqueda . '%'
        ]);

        return $consulta->fetchAll();
    }

    // Buscamos una carrera mediante su identificador
    public function buscarPorId(int $idCarrera): ?array
    {
        $sql = "
            SELECT
                id_carrera,
                nombre_carrera
            FROM carreras
            WHERE id_carrera = :id_carrera
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_carrera' => $idCarrera
        ]);

        $resultado = $consulta->fetch();

        return $resultado ?: null;
    }

    // Evitamos registrar dos carreras con el mismo nombre
    public function existeNombre(
        string $nombreCarrera,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM carreras
            WHERE nombre_carrera = :nombre_carrera
        ";

        $parametros = [
            'nombre_carrera' => $nombreCarrera
        ];

        if ($idExcluir !== null) {
            $sql .= " AND id_carrera != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Registramos una carrera nueva
    public function crear(string $nombreCarrera): bool
    {
        $sql = "
            INSERT INTO carreras (nombre_carrera)
            VALUES (:nombre_carrera)
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'nombre_carrera' => $nombreCarrera
        ]);
    }

    // Actualizamos el nombre de una carrera existente
    public function actualizar(
        int $idCarrera,
        string $nombreCarrera
    ): bool {
        $sql = "
            UPDATE carreras
            SET nombre_carrera = :nombre_carrera
            WHERE id_carrera = :id_carrera
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'nombre_carrera' => $nombreCarrera,
            'id_carrera' => $idCarrera
        ]);
    }

    // Revisamos si la carrera está siendo utilizada
    public function tieneRegistrosRelacionados(int $idCarrera): bool
    {
        $sql = "
            SELECT (
                SELECT COUNT(*)
                FROM estudiantes
                WHERE id_carrera = :id_estudiantes
            ) + (
                SELECT COUNT(*)
                FROM materias
                WHERE id_carrera = :id_materias
            ) AS total
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_estudiantes' => $idCarrera,
            'id_materias' => $idCarrera
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Eliminamos la carrera cuando no tiene registros relacionados
    public function eliminar(int $idCarrera): bool
    {
        $sql = "
            DELETE FROM carreras
            WHERE id_carrera = :id_carrera
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_carrera' => $idCarrera
        ]);
    }
}