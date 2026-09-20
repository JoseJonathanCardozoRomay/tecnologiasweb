<?php
class TutorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_tutor = t.id_tutor) AS total_materias,
                       (SELECT COUNT(*) FROM disponibilidad_tutor dt WHERE dt.id_tutor = t.id_tutor) AS total_horarios
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodosOrdenados($orden, $dir)
    {
        $columnas = [
            'nombre' => 'u.nombre',
            'especialidad' => 't.especialidad',
            'materias' => 'total_materias',
            'horarios' => 'total_horarios',
        ];
        $orden = $columnas[$orden] ?? $columnas['nombre'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_tutor = t.id_tutor) AS total_materias,
                       (SELECT COUNT(*) FROM disponibilidad_tutor dt WHERE dt.id_tutor = t.id_tutor) AS total_horarios
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY {$orden} {$dir}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPaginadas($q, $orden, $dir, $limite, $offset)
    {
        $columnas = ['nombre' => 'u.nombre', 'especialidad' => 't.especialidad', 'materias' => 'total_materias', 'horarios' => 'total_horarios'];
        $ordenSql = $columnas[$orden] ?? $columnas['nombre'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                       u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado,
                       (SELECT COUNT(*) FROM tutor_materia tm WHERE tm.id_tutor = t.id_tutor) AS total_materias,
                       (SELECT COUNT(*) FROM disponibilidad_tutor dt WHERE dt.id_tutor = t.id_tutor) AS total_horarios
                FROM tutores t INNER JOIN usuarios u ON t.id_usuario = u.id_usuario";
        $params = [];
        if ($q !== '') {
            $sql .= " WHERE CONCAT_WS(' ', u.nombre, u.apellido, u.correo, u.usuario, t.especialidad) LIKE :q ESCAPE '\\\\'";
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
        $sql = 'SELECT COUNT(*) FROM tutores t INNER JOIN usuarios u ON t.id_usuario = u.id_usuario';
        $stmt = $this->pdo->prepare($q !== '' ? $sql . " WHERE CONCAT_WS(' ', u.nombre, u.apellido, u.correo, u.usuario, t.especialidad) LIKE :q ESCAPE '\\\\'" : $sql);
        if ($q !== '') $stmt->bindValue(':q', valorBusquedaLike($q), PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id_tutor)
    {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.correo, u.telefono, u.usuario, u.estado
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_tutor = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_tutor]);
        return $stmt->fetch();
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.correo, u.telefono
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    public function obtenerPerfilCompleto($id_tutor)
    {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.correo, u.telefono, u.usuario,
                       (SELECT COUNT(*) FROM tutorias tu WHERE tu.id_tutor = t.id_tutor AND tu.estado = 'realizada') AS sesiones_realizadas,
                       (SELECT ROUND(AVG(ev.calificacion), 1) FROM evaluaciones_tutoria ev INNER JOIN tutorias t2 ON ev.id_tutoria = t2.id_tutoria WHERE t2.id_tutor = t.id_tutor) AS calificacion_promedio
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_tutor = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_tutor]);
        return $stmt->fetch();
    }

    public function actualizarPerfilCompleto($id_tutor, $datos)
    {
        $sets = [];
        $params = [];

        if (isset($datos['especialidad'])) {
            $sets[] = 'especialidad = :especialidad';
            $params[':especialidad'] = trim($datos['especialidad']);
        }
        if (isset($datos['biografia'])) {
            $sets[] = 'biografia = :biografia';
            $params[':biografia'] = trim($datos['biografia']);
        }
        if (isset($datos['perfil_linkedin'])) {
            $sets[] = 'perfil_linkedin = :linkedin';
            $params[':linkedin'] = trim($datos['perfil_linkedin']);
        }
        if (isset($datos['certificaciones'])) {
            $sets[] = 'certificaciones = :certificaciones';
            $params[':certificaciones'] = trim($datos['certificaciones']);
        }
        if (isset($datos['areas_expertise'])) {
            $sets[] = 'areas_expertise = :expertise';
            $params[':expertise'] = trim($datos['areas_expertise']);
        }
        if (array_key_exists('foto_perfil', $datos)) {
            $sets[] = 'foto_perfil = :foto';
            $params[':foto'] = $datos['foto_perfil'] ?: null;
        }

        if (empty($sets)) {
            return false;
        }

        $setsSql = implode(', ', $sets);
        $params[':id'] = $id_tutor;

        $sql = "UPDATE tutores SET {$setsSql} WHERE id_tutor = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function obtenerBloquesSeleccionados($id_tutor)
    {
        $sql = "SELECT tbs.id_bloque, bh.nombre_bloque, bh.hora_inicio, bh.hora_fin, bh.descripcion
                FROM tutor_bloque_seleccionado tbs
                INNER JOIN bloques_horarios bh ON tbs.id_bloque = bh.id_bloque
                WHERE tbs.id_tutor = :id_tutor
                ORDER BY bh.hora_inicio ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function asignarBloques($id_tutor, $bloques_ids = [])
    {
        $this->pdo->prepare("DELETE FROM tutor_bloque_seleccionado WHERE id_tutor = :id_tutor")->execute([':id_tutor' => $id_tutor]);
        if (!empty($bloques_ids)) {
            $stmt = $this->pdo->prepare("INSERT INTO tutor_bloque_seleccionado (id_tutor, id_bloque) VALUES (:id_tutor, :id_bloque)");
            foreach ($bloques_ids as $id_bloque) {
                $stmt->execute([
                    ':id_tutor'   => $id_tutor,
                    ':id_bloque' => $id_bloque
                ]);
            }
        }
        return true;
    }

    // Materias que domina el tutor
    public function obtenerMaterias($id_tutor)
    {
        $sql = "SELECT m.id_materia, m.nombre_materia, c.nombre_carrera
                FROM materias m
                INNER JOIN tutor_materia tm ON m.id_materia = tm.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                WHERE tm.id_tutor = :id_tutor
                ORDER BY m.nombre_materia ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function asignarMaterias($id_tutor, $materias_ids = [])
    {
        $this->pdo->prepare("DELETE FROM tutor_materia WHERE id_tutor = :id_tutor")->execute([':id_tutor' => $id_tutor]);
        if (!empty($materias_ids)) {
            $stmt = $this->pdo->prepare("INSERT INTO tutor_materia (id_tutor, id_materia) VALUES (:id_tutor, :id_materia)");
            foreach ($materias_ids as $id_materia) {
                $stmt->execute([
                    ':id_tutor'   => $id_tutor,
                    ':id_materia' => $id_materia
                ]);
            }
        }
        return true;
    }

    // Tutores disponibles para una materia específica (para agendar tutorías)
    public function obtenerTutoresPorMateria($id_materia)
    {
        $sql = "SELECT t.id_tutor, u.nombre, u.apellido, t.especialidad
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN tutor_materia tm ON t.id_tutor = tm.id_tutor
                WHERE tm.id_materia = :id_materia AND u.estado = 'activo'
                ORDER BY u.nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_materia' => $id_materia]);
        return $stmt->fetchAll();
    }

    public function tutorDictaMateria($idTutor, $idMateria)
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tutor_materia WHERE id_tutor = :tutor AND id_materia = :materia');
        $stmt->execute([':tutor' => $idTutor, ':materia' => $idMateria]);
        return (bool) $stmt->fetchColumn();
    }

    public function tieneDisponibilidad($idTutor, $dia, $inicio, $fin)
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM disponibilidad_tutor WHERE id_tutor = :tutor AND dia_semana = :dia AND hora_inicio <= :inicio AND hora_fin >= :fin');
        $stmt->execute([':tutor' => $idTutor, ':dia' => $dia, ':inicio' => $inicio, ':fin' => $fin]);
        return (bool) $stmt->fetchColumn();
    }

    // Horarios de disponibilidad
    public function obtenerDisponibilidad($id_tutor)
    {
        $sql = "SELECT * FROM disponibilidad_tutor WHERE id_tutor = :id_tutor ORDER BY 
                FIELD(dia_semana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'), hora_inicio ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function agregarDisponibilidad($id_tutor, $dia_semana, $hora_inicio, $hora_fin)
    {
        if ($hora_fin <= $hora_inicio) return false;
        $cruce = $this->pdo->prepare('SELECT 1 FROM disponibilidad_tutor WHERE id_tutor = :tutor AND dia_semana = :dia AND hora_inicio < :fin AND hora_fin > :inicio');
        $cruce->execute([':tutor' => $id_tutor, ':dia' => $dia_semana, ':inicio' => $hora_inicio, ':fin' => $hora_fin]);
        if ($cruce->fetchColumn()) return false;
        $stmt = $this->pdo->prepare("INSERT INTO disponibilidad_tutor (id_tutor, dia_semana, hora_inicio, hora_fin) 
                                     VALUES (:id_tutor, :dia, :inicio, :fin)");
        return $stmt->execute([
            ':id_tutor' => $id_tutor,
            ':dia'      => $dia_semana,
            ':inicio'   => $hora_inicio,
            ':fin'      => $hora_fin
        ]);
    }

    public function eliminarDisponibilidad($id_disp, $id_tutor)
    {
        $stmt = $this->pdo->prepare("DELETE FROM disponibilidad_tutor WHERE id_disponibilidad = :id AND id_tutor = :id_tutor");
        return $stmt->execute([
            ':id'       => $id_disp,
            ':id_tutor' => $id_tutor
        ]);
    }
}
