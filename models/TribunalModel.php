<?php
class TribunalModel
{
    private const CANTIDAD_TRIBUNALES = 2;
    private const MAXIMO_TRIBUNALES   = 5;

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private static function escapeLike(string $valor): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $valor);
    }

    public function cantidadPorTutoriaEsperada(): int
    {
        return self::CANTIDAD_TRIBUNALES;
    }

    public function maximoTribunales(): int
    {
        return self::MAXIMO_TRIBUNALES;
    }

    public function asignar($id_tutoria, array $id_usuarios, $reemplazar = false)
    {
        $id_tutoria = (int) $id_tutoria;
        $propuestos = array_values(array_filter(array_map('intval', $id_usuarios), fn($id) => $id > 0));

        if (count($propuestos) !== count(array_unique($propuestos))) {
            throw new InvalidArgumentException('Un mismo docente no puede ser asignado dos veces como tribunal del mismo expediente.');
        }
        if (count($propuestos) < self::CANTIDAD_TRIBUNALES) {
            throw new InvalidArgumentException('Debes asignar al menos ' . self::CANTIDAD_TRIBUNALES . ' tribunales.');
        }
        if (count($propuestos) > self::MAXIMO_TRIBUNALES) {
            throw new InvalidArgumentException('Puedes asignar como máximo ' . self::MAXIMO_TRIBUNALES . ' tribunales.');
        }

        $info = $this->infoAsignacion($id_tutoria);
        if (!$info) {
            throw new InvalidArgumentException('La tutoría seleccionada no existe o no corresponde a una Modalidad de Grado.');
        }

        $yaAsignados = array_values(array_filter(array_unique(
            array_map('intval', array_column($this->obtenerPorTutoria($id_tutoria), 'id_usuario'))
        )));

        // En modo agregar se ignoran los miembros que ya forman parte del tribunal
        // (esto permite añadir más tarde un tribunal opcional sin repetir los existentes).
        $nuevos = $reemplazar
            ? $propuestos
            : array_values(array_filter($propuestos, fn($id) => !in_array($id, $yaAsignados, true)));

        $total = $reemplazar ? count($nuevos) : count($yaAsignados) + count($nuevos);
        if ($total < self::CANTIDAD_TRIBUNALES) {
            throw new InvalidArgumentException('La tutoría debe tener al menos ' . self::CANTIDAD_TRIBUNALES . ' tribunales.');
        }
        if ($total > self::MAXIMO_TRIBUNALES) {
            throw new InvalidArgumentException('Puedes asignar como máximo ' . self::MAXIMO_TRIBUNALES . ' tribunales por expediente.');
        }

        if (empty($nuevos) && !$reemplazar) {
            throw new InvalidArgumentException('Los tribunales seleccionados ya están asignados a este expediente.');
        }

        // Docentes válidos: activos y pertenecientes a la carrera del estudiante
        // (el tutor de la tutoría queda fuera automáticamente).
        $docentesValidos = array_map(
            'intval',
            array_column($this->docentesDisponibles($id_tutoria, null, (int) $info['id_carrera']), 'id_usuario')
        );
        if (!$reemplazar) {
            $docentesValidos = array_values(array_diff($docentesValidos, $yaAsignados));
        }

        $nombreCarrera = (string) ($info['nombre_carrera'] ?? '');
        foreach ($nuevos as $id) {
            if (!in_array($id, $docentesValidos, true)) {
                throw new InvalidArgumentException(
                    'El docente seleccionado no pertenece a la carrera ' . $nombreCarrera .
                    ' o no está habilitado como tribunal del expediente.'
                );
            }
        }

        try {
            $this->pdo->beginTransaction();

            if ($reemplazar) {
                $this->pdo->prepare("DELETE FROM tribunales WHERE id_tutoria = :id_tutoria")
                    ->execute([':id_tutoria' => $id_tutoria]);
            }

            $stmt = $this->pdo->prepare(
                "INSERT IGNORE INTO tribunales (id_usuario, id_tutoria) VALUES (:id_usuario, :id_tutoria)"
            );
            foreach ($nuevos as $id) {
                $stmt->execute([':id_usuario' => $id, ':id_tutoria' => $id_tutoria]);
            }

            $this->pdo->commit();
            return count($nuevos);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al asignar los tribunales.');
        }
    }

    public function editarMiembro($id_tribunal, $id_usuario)
    {
        $id_usuario = (int) $id_usuario;
        if ($id_usuario <= 0) {
            throw new InvalidArgumentException('Debes seleccionar un docente para el tribunal.');
        }

        $stmt = $this->pdo->prepare(
            "SELECT tr.id_tribunal, tr.id_tutoria, tr.id_usuario FROM tribunales tr WHERE tr.id_tribunal = :id_tribunal"
        );
        $stmt->execute([':id_tribunal' => (int) $id_tribunal]);
        $tribunal = $stmt->fetch();
        if (!$tribunal) {
            throw new InvalidArgumentException('El tribunal seleccionado no existe.');
        }

        $id_tutoria = (int) $tribunal['id_tutoria'];
        $id_actual  = (int) $tribunal['id_usuario'];

        $info = $this->infoAsignacion($id_tutoria);
        $nombreCarrera = (string) ($info['nombre_carrera'] ?? '');

        $docentesValidos = array_map('intval', array_column($this->docentesDisponibles($id_tutoria, null, (int) ($info['id_carrera'] ?? 0)), 'id_usuario'));
        $yaAsignados = array_values(array_filter(array_unique(
            array_map('intval', array_column($this->obtenerPorTutoria($id_tutoria), 'id_usuario'))
        )));
        $otrosMiembros = array_values(array_filter($yaAsignados, fn($id) => $id !== $id_actual));

        if (in_array($id_usuario, $otrosMiembros, true)) {
            throw new InvalidArgumentException('Ese docente ya integra el tribunal de este expediente.');
        }
        if (!in_array($id_usuario, $docentesValidos, true)) {
            throw new InvalidArgumentException(
                'El docente seleccionado no pertenece a la carrera ' . $nombreCarrera .
                ' o no puede ser tribunal de esta tutoría (no puede ser el tutor asignado).'
            );
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE tribunales SET id_usuario = :id_usuario WHERE id_tribunal = :id_tribunal"
            );
            $stmt->execute([':id_usuario' => $id_usuario, ':id_tribunal' => (int) $tribunal['id_tribunal']]);

            return $id_tutoria;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al modificar el tribunal.');
        }
    }

    public function eliminarMiembro($id_tribunal)
    {
        $stmt = $this->pdo->prepare(
            "SELECT tr.id_tribunal, tr.id_tutoria FROM tribunales tr WHERE tr.id_tribunal = :id_tribunal"
        );
        $stmt->execute([':id_tribunal' => (int) $id_tribunal]);
        $tribunal = $stmt->fetch();
        if (!$tribunal) {
            throw new InvalidArgumentException('El tribunal seleccionado no existe.');
        }

        $cantidad = (int) $this->contarPorTutoria((int) $tribunal['id_tutoria']);
        if ($cantidad <= self::CANTIDAD_TRIBUNALES) {
            throw new InvalidArgumentException(
                'Debes mantener al menos ' . self::CANTIDAD_TRIBUNALES .
                ' tribunales (Tribunal 1 y Tribunal 2 son obligatorios). Solo puedes eliminar tribunales opcionales.'
            );
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM tribunales WHERE id_tribunal = :id_tribunal");
            $stmt->execute([':id_tribunal' => (int) $tribunal['id_tribunal']]);

            return (int) $tribunal['id_tutoria'];
        } catch (PDOException $e) {
            throw new RuntimeException('Error al eliminar el tribunal.');
        }
    }

    public function contarPorTutoria($id_tutoria)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tribunales WHERE id_tutoria = :id_tutoria");
        $stmt->execute([':id_tutoria' => (int) $id_tutoria]);

        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorTutoria($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT tr.*, u.nombre, u.apellido, u.correo
             FROM tribunales tr
             INNER JOIN usuarios u ON tr.id_usuario = u.id_usuario
             WHERE tr.id_tutoria = :id_tutoria
             ORDER BY tr.id_tribunal ASC"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetchAll();
    }

    public function infoAsignacion($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.id_tutoria, t.tipo, t.estado,
                    es.id_estudiante, es.id_carrera, c.nombre_carrera,
                    est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                    tt.id_tutor, tu.id_usuario AS id_usuario_tutor,
                    tu.nombre AS tutor_nombre, tu.apellido AS tutor_apellido
             FROM tutorias t
             INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
             INNER JOIN carreras c ON es.id_carrera = c.id_carrera
             INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
             INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
             INNER JOIN usuarios tu ON tt.id_usuario = tu.id_usuario
             WHERE t.id_tutoria = :id_tutoria
             LIMIT 1"
        );
        $stmt->execute([':id_tutoria' => (int) $id_tutoria]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($info && ($info['tipo'] ?? '') === 'grado') ? $info : null;
    }

    public function docentesDisponibles($id_tutoria, $q = null, $id_carrera = null)
    {
        $id_tutoria = (int) $id_tutoria;

        if ($id_carrera === null) {
            $info = $this->infoAsignacion($id_tutoria);
            $id_carrera = $info ? (int) $info['id_carrera'] : 0;
        } else {
            $id_carrera = (int) $id_carrera;
        }

        $sql = "SELECT DISTINCT u.id_usuario, u.nombre, u.apellido, u.correo,
                       t.id_tutor, t.especialidad,
                       (SELECT GROUP_CONCAT(m.nombre_materia ORDER BY m.nombre_materia SEPARATOR ', ')
                          FROM tutor_materia tm
                          INNER JOIN materias m ON tm.id_materia = m.id_materia
                         WHERE tm.id_tutor = t.id_tutor AND m.id_carrera = :id_carrera2) AS materias_carrera
                FROM usuarios u
                INNER JOIN tutores t ON u.id_usuario = t.id_usuario
                INNER JOIN docente_carreras dc ON dc.id_tutor = t.id_tutor
                WHERE u.id_rol = 2
                  AND u.estado = 'activo'
                  AND dc.id_carrera = :id_carrera
                  AND u.id_usuario NOT IN (
                      SELECT tut.id_usuario
                      FROM tutorias tu
                      INNER JOIN tutores tut ON tu.id_tutor = tut.id_tutor
                      WHERE tu.id_tutoria = :id_tutoria
                  )";
        $params = [
            ':id_carrera2' => $id_carrera,
            ':id_carrera'  => $id_carrera,
            ':id_tutoria'  => $id_tutoria,
        ];

        $q = $q !== null ? trim((string) $q) : '';
        if ($q !== '') {
            $like = '%' . self::escapeLike($q) . '%';
            $sql .= " AND (t.especialidad LIKE :q_esp
                           OR EXISTS (
                               SELECT 1
                               FROM tutor_materia tm2
                               INNER JOIN materias m2 ON tm2.id_materia = m2.id_materia
                               WHERE tm2.id_tutor = t.id_tutor
                                 AND m2.nombre_materia LIKE :q_mat
                           ))";
            $params[':q_esp'] = $like;
            $params[':q_mat'] = $like;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}