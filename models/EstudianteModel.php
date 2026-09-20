<?php
class EstudianteModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT e.id_estudiante, e.id_usuario, e.id_carrera, e.semestre, e.registro_universitario,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutorias t WHERE t.id_estudiante = e.id_estudiante) AS total_tutorias
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodosOrdenados($orden, $dir)
    {
        $columnas = [
            'nombre' => 'u.nombre',
            'registro' => 'e.registro_universitario',
            'carrera' => 'c.nombre_carrera',
            'semestre' => 'e.semestre',
            'tutorias' => 'total_tutorias',
        ];
        $orden = $columnas[$orden] ?? $columnas['nombre'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT e.id_estudiante, e.id_usuario, e.id_carrera, e.semestre, e.registro_universitario,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutorias t WHERE t.id_estudiante = e.id_estudiante) AS total_tutorias
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY {$orden} {$dir}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPaginadas($q, $orden, $dir, $limite, $offset)
    {
        $columnas = ['nombre' => 'u.nombre', 'registro' => 'e.registro_universitario', 'carrera' => 'c.nombre_carrera', 'semestre' => 'e.semestre', 'tutorias' => 'total_tutorias'];
        $ordenSql = $columnas[$orden] ?? $columnas['nombre'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT e.id_estudiante, e.id_usuario, e.id_carrera, e.semestre, e.registro_universitario,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado, c.nombre_carrera,
                       (SELECT COUNT(*) FROM tutorias t WHERE t.id_estudiante = e.id_estudiante) AS total_tutorias
                FROM estudiantes e INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera";
        $params = [];
        if ($q !== '') {
            $sql .= " WHERE CONCAT_WS(' ', u.nombre, u.apellido, u.correo, u.usuario, e.registro_universitario, c.nombre_carrera) LIKE :q ESCAPE '\\\\'";
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
        $sql = 'SELECT COUNT(*) FROM estudiantes e INNER JOIN usuarios u ON e.id_usuario = u.id_usuario INNER JOIN carreras c ON e.id_carrera = c.id_carrera';
        $stmt = $this->pdo->prepare($q !== '' ? $sql . " WHERE CONCAT_WS(' ', u.nombre, u.apellido, u.correo, u.usuario, e.registro_universitario, c.nombre_carrera) LIKE :q ESCAPE '\\\\'" : $sql);
        if ($q !== '') $stmt->bindValue(':q', valorBusquedaLike($q), PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id_estudiante)
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.correo, u.telefono, u.usuario, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_estudiante]);
        return $stmt->fetch();
    }

    public function existeRegistroUniversitario($ru, $excluirId = null)
    {
        $sql = 'SELECT 1 FROM estudiantes WHERE registro_universitario = :ru';
        $params = [':ru' => $ru];
        if ($excluirId !== null) {
            $sql .= ' AND id_estudiante <> :excluir_id';
            $params[':excluir_id'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Devuelve el estudiante asociado a un usuario.
     * Usa LEFT JOIN con carreras (no INNER) para que un estudiante SIN carrera
     * asignada (id_carrera NULL) siga apareciendo: así el panel puede mostrar un
     * mensaje claro en lugar de un error genérico de "perfil incompleto".
     */
    public function obtenerPorUsuario($id_usuario)
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.correo, u.telefono, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                LEFT JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    /**
     * Devuelve la carrera del estudiante (con su id) o null si no tiene carrera asignada.
     */
    public function obtenerCarreraPorEstudiante($id_estudiante)
    {
        $sql = "SELECT c.id_carrera, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_estudiante]);
        $carrera = $stmt->fetch();
        return $carrera ?: null;
    }

    public function guardarOActualizar($id_usuario, $id_carrera, $semestre, $registro_universitario)
    {
        $sql = "INSERT INTO estudiantes (id_usuario, id_carrera, semestre, registro_universitario)
                VALUES (:id_usuario, :id_carrera, :semestre, :ru)
                ON DUPLICATE KEY UPDATE id_carrera = VALUES(id_carrera), semestre = VALUES(semestre), registro_universitario = VALUES(registro_universitario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_carrera' => $id_carrera,
            ':semestre'   => $semestre,
            ':ru'         => trim($registro_universitario)
        ]);
    }

    /**
     * Verifica si un estudiante tiene carrera asignada.
     * Útil para validaciones de negocio antes de permitir solicitudes de tutoría.
     */
    public function tieneCarreraAsignada($id_estudiante)
    {
        $sql = "SELECT id_carrera FROM estudiantes WHERE id_estudiante = :id AND id_carrera IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_estudiante]);
        return (bool) $stmt->fetchColumn();
    }
}
