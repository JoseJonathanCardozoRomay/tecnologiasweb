<?php
class MateriaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_materia = m.id_materia) AS total_tutores
                FROM materias m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                ORDER BY m.nombre_materia ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodasOrdenadas($orden, $dir)
    {
        $columnas = [
            'id' => 'm.id_materia',
            'nombre' => 'm.nombre_materia',
            'carrera' => 'c.nombre_carrera',
            'tutores' => 'total_tutores',
        ];
        $orden = $columnas[$orden] ?? $columnas['nombre'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_materia = m.id_materia) AS total_tutores
                FROM materias m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                ORDER BY {$orden} {$dir}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPaginadas($q, $id_carrera, $orden, $dir, $limite, $offset)
    {
        $columnas = ['id' => 'm.id_materia', 'nombre' => 'm.nombre_materia', 'carrera' => 'c.nombre_carrera', 'tutores' => 'total_tutores'];
        $ordenSql = $columnas[$orden] ?? $columnas['nombre'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera, c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_materia = m.id_materia) AS total_tutores
                FROM materias m LEFT JOIN carreras c ON m.id_carrera = c.id_carrera";
        $params = [];
        $where = [];
        if ($q !== '') {
            $where[] = "CONCAT_WS(' ', m.nombre_materia, c.nombre_carrera) LIKE :q ESCAPE '\\\\'";
            $params[':q'] = valorBusquedaLike($q);
        }
        if ($id_carrera > 0) {
            $where[] = "m.id_carrera = :id_carrera";
            $params[':id_carrera'] = $id_carrera;
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY {$ordenSql} {$dirSql} LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($q = '', $id_carrera = 0)
    {
        $sql = 'SELECT COUNT(*) FROM materias m LEFT JOIN carreras c ON m.id_carrera = c.id_carrera';
        $params = [];
        $where = [];
        if ($q !== '') {
            $where[] = "CONCAT_WS(' ', m.nombre_materia, c.nombre_carrera) LIKE :q ESCAPE '\\\\'";
            $params[':q'] = valorBusquedaLike($q);
        }
        if ($id_carrera > 0) {
            $where[] = "m.id_carrera = :id_carrera";
            $params[':id_carrera'] = $id_carrera;
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM materias WHERE id_materia = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeNombre($nombre, $idCarrera, $excluirId = null)
    {
        $sql = 'SELECT 1 FROM materias WHERE nombre_materia = :nombre AND id_carrera <=> :id_carrera';
        $params = [':nombre' => $nombre, ':id_carrera' => $idCarrera];
        if ($excluirId !== null) {
            $sql .= ' AND id_materia <> :excluir_id';
            $params[':excluir_id'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function obtenerPorCarrera($id_carrera)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM materias WHERE id_carrera = :id_carrera ORDER BY nombre_materia ASC");
        $stmt->execute([':id_carrera' => $id_carrera]);
        return $stmt->fetchAll();
    }

    /**
     * Materias que el estudiante PUEDE solicitar: SOLO las de su carrera.
     * - Si la carrera es 0/null, devuelve array vacío (el estudiante sin carrera
     *   no tiene materias disponibles).
     * - Solo se incluyen materias con al menos un tutor activo.
     * - NO se devuelven materias de otras carreras ni materias "generales".
     */
    public function obtenerDisponiblesParaCarrera($idCarrera)
    {
        $idCarrera = (int) $idCarrera;
        if ($idCarrera <= 0) {
            return [];
        }
        $sql = "SELECT m.id_materia, m.nombre_materia, m.id_carrera, c.nombre_carrera
                FROM materias m
                INNER JOIN carreras c ON c.id_carrera = m.id_carrera
                WHERE m.id_carrera = :carrera
                  AND EXISTS (SELECT 1 FROM tutor_materia tm
                              INNER JOIN tutores t ON t.id_tutor = tm.id_tutor
                              INNER JOIN usuarios u ON u.id_usuario = t.id_usuario
                              WHERE tm.id_materia = m.id_materia AND u.estado = 'activo')
                ORDER BY m.nombre_materia";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':carrera' => $idCarrera]);
        return $stmt->fetchAll();
    }

    public function obtenerTodasConTutorActivo()
    {
        return $this->pdo->query("SELECT m.id_materia, m.nombre_materia, m.id_carrera, c.nombre_carrera FROM materias m LEFT JOIN carreras c ON c.id_carrera = m.id_carrera WHERE EXISTS (SELECT 1 FROM tutor_materia tm INNER JOIN tutores t ON t.id_tutor = tm.id_tutor INNER JOIN usuarios u ON u.id_usuario = t.id_usuario WHERE tm.id_materia = m.id_materia AND u.estado = 'activo') ORDER BY m.nombre_materia")->fetchAll();
    }

    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("INSERT INTO materias (nombre_materia, id_carrera) VALUES (:nombre, :id_carrera)");
        return $stmt->execute([
            ':nombre'     => trim($datos['nombre_materia']),
            ':id_carrera' => !empty($datos['id_carrera']) ? $datos['id_carrera'] : null
        ]);
    }

    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("UPDATE materias SET nombre_materia = :nombre, id_carrera = :id_carrera WHERE id_materia = :id");
        return $stmt->execute([
            ':nombre'     => trim($datos['nombre_materia']),
            ':id_carrera' => !empty($datos['id_carrera']) ? $datos['id_carrera'] : null,
            ':id'         => $id
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM materias WHERE id_materia = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarDependencias($id)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tutorias WHERE id_materia = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }
}
