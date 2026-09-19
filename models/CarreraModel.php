<?php
class CarreraModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT c.id_carrera, c.nombre_carrera,
                       (SELECT COUNT(*) FROM materias m WHERE m.id_carrera = c.id_carrera) AS total_materias,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_carrera = c.id_carrera) AS total_estudiantes
                FROM carreras c
                ORDER BY c.nombre_carrera ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodasOrdenadas($orden, $dir)
    {
        $columnas = [
            'id' => 'c.id_carrera',
            'nombre' => 'c.nombre_carrera',
            'materias' => 'total_materias',
            'estudiantes' => 'total_estudiantes',
        ];
        $orden = $columnas[$orden] ?? $columnas['nombre'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT c.id_carrera, c.nombre_carrera,
                       (SELECT COUNT(*) FROM materias m WHERE m.id_carrera = c.id_carrera) AS total_materias,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_carrera = c.id_carrera) AS total_estudiantes
                FROM carreras c
                ORDER BY {$orden} {$dir}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPaginadas($q, $orden, $dir, $limite, $offset)
    {
        $columnas = ['id' => 'c.id_carrera', 'nombre' => 'c.nombre_carrera', 'materias' => 'total_materias', 'estudiantes' => 'total_estudiantes'];
        $ordenSql = $columnas[$orden] ?? $columnas['nombre'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT c.id_carrera, c.nombre_carrera,
                       (SELECT COUNT(*) FROM materias m WHERE m.id_carrera = c.id_carrera) AS total_materias,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_carrera = c.id_carrera) AS total_estudiantes
                FROM carreras c";
        $params = [];
        if ($q !== '') {
            $sql .= " WHERE c.nombre_carrera LIKE :q ESCAPE '\\\\'";
            $params[':q'] = valorBusquedaLike($q);
        }
        $sql .= " ORDER BY {$ordenSql} {$dirSql} LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        if ($q !== '') $stmt->bindValue(':q', $params[':q'], PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($q = '')
    {
        $sql = 'SELECT COUNT(*) FROM carreras';
        $stmt = $this->pdo->prepare($q !== '' ? $sql . " WHERE nombre_carrera LIKE :q ESCAPE '\\\\'" : $sql);
        if ($q !== '') $stmt->bindValue(':q', valorBusquedaLike($q), PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carreras WHERE id_carrera = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeNombre($nombre, $excluirId = null)
    {
        $sql = 'SELECT 1 FROM carreras WHERE nombre_carrera = :nombre';
        $params = [':nombre' => $nombre];
        if ($excluirId !== null) {
            $sql .= ' AND id_carrera <> :excluir_id';
            $params[':excluir_id'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function crear($nombre)
    {
        $stmt = $this->pdo->prepare("INSERT INTO carreras (nombre_carrera) VALUES (:nombre)");
        return $stmt->execute([':nombre' => trim($nombre)]);
    }

    public function actualizar($id, $nombre)
    {
        $stmt = $this->pdo->prepare("UPDATE carreras SET nombre_carrera = :nombre WHERE id_carrera = :id");
        return $stmt->execute([
            ':nombre' => trim($nombre),
            ':id'     => $id
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM carreras WHERE id_carrera = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarDependencias($id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                (SELECT COUNT(*) FROM materias WHERE id_carrera = :id_materias) AS materias,
                (SELECT COUNT(*) FROM estudiantes WHERE id_carrera = :id_estudiantes) AS estudiantes'
        );
        $stmt->execute([':id_materias' => $id, ':id_estudiantes' => $id]);
        return $stmt->fetch();
    }
}
