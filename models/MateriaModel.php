<?php
declare(strict_types=1);

/**
 * Modelo del catálogo de materias.
 *
 * Las materias se conservan para mantener el historial de tutorías y
 * asignaciones; su baja lógica se representa mediante estado='inactivo'.
 */
class MateriaModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodas(?string $busqueda = null, ?int $idCarrera = null, ?string $estado = null): array
    {
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera, m.estado,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_materia = m.id_materia) AS total_tutores
                FROM materias m
                INNER JOIN carreras c ON c.id_carrera = m.id_carrera
                WHERE 1=1";
        $params = [];

        if ($busqueda !== null && $busqueda !== '') {
            $sql .= ' AND LOWER(m.nombre_materia) LIKE LOWER(:busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        if ($idCarrera !== null) {
            $sql .= ' AND m.id_carrera = :carrera';
            $params[':carrera'] = $idCarrera;
        }
        if ($estado !== null && in_array($estado, ['activo', 'inactivo'], true)) {
            $sql .= ' AND m.estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' ORDER BY m.id_carrera ASC, m.id_materia ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerActivas(?int $incluirId = null, ?int $idCarrera = null): array
    {
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera, m.estado
                FROM materias m
                INNER JOIN carreras c ON c.id_carrera = m.id_carrera
                WHERE (m.estado='activo' AND c.estado='activo')";
        $params = [];

        if ($incluirId !== null) {
            $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera, m.estado
                    FROM materias m
                    INNER JOIN carreras c ON c.id_carrera = m.id_carrera
                    WHERE ((m.estado='activo' AND c.estado='activo') OR m.id_materia=:actual)";
            $params[':actual'] = $incluirId;
        }
        if ($idCarrera !== null) {
            $sql .= ' AND m.id_carrera = :carrera';
            $params[':carrera'] = $idCarrera;
        }

        $sql .= ' ORDER BY m.id_carrera ASC, m.id_materia ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*, c.nombre_carrera FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera WHERE m.id_materia=:id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function obtenerPorCarrera(int $idCarrera): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.* FROM materias m
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             WHERE m.id_carrera=:id_carrera AND m.estado='activo' AND c.estado='activo'
             ORDER BY m.id_materia ASC"
        );
        $stmt->execute([':id_carrera' => $idCarrera]);
        return $stmt->fetchAll();
    }

    public function existeNombre(string $nombre, ?int $excepto = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM materias WHERE LOWER(TRIM(nombre_materia)) = LOWER(TRIM(:nombre))';
        $params = [':nombre' => $nombre];
        if ($excepto !== null) {
            $sql .= ' AND id_materia <> :id';
            $params[':id'] = $excepto;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO materias (nombre_materia, id_carrera, estado) VALUES (:nombre, :carrera, 'activo')"
        );
        return $stmt->execute([
            ':nombre' => $datos['nombre_materia'],
            ':carrera' => $datos['id_carrera'],
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE materias SET nombre_materia=:nombre, id_carrera=:carrera WHERE id_materia=:id'
        );
        return $stmt->execute([
            ':nombre' => $datos['nombre_materia'],
            ':carrera' => $datos['id_carrera'],
            ':id' => $id,
        ]);
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE materias SET estado=:estado WHERE id_materia=:id'
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
