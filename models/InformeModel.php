<?php
class InformeModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_tutoria, $numero_informe, $porcentaje_avance, $descripcion_avance, $fecha_limite = null)
    {
        $porcentaje_avance = (int) $porcentaje_avance;
        if ($porcentaje_avance < 0 || $porcentaje_avance > 100) {
            throw new InvalidArgumentException('El porcentaje de avance debe estar entre 0 y 100.');
        }

        if ($numero_informe < 1) {
            throw new InvalidArgumentException('El número de informe debe ser mayor a cero.');
        }

        $descripcion_avance = trim($descripcion_avance ?? '');
        if ($descripcion_avance === '') {
            throw new InvalidArgumentException('Debes describir el avance del informe.');
        }

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO informes_avance
                    (id_tutoria, numero_informe, fecha_limite, porcentaje_avance, descripcion_avance)
                 VALUES
                    (:id_tutoria, :numero_informe, :fecha_limite, :porcentaje_avance, :descripcion_avance)"
            );
            $stmt->execute([
                ':id_tutoria'          => $id_tutoria,
                ':numero_informe'      => $numero_informe,
                ':fecha_limite'        => $fecha_limite,
                ':porcentaje_avance'   => $porcentaje_avance,
                ':descripcion_avance'  => $descripcion_avance,
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new InvalidArgumentException('Ya existe el informe #' . $numero_informe . ' para esta tutoría.');
            }
            throw new RuntimeException('Error al registrar el informe de avance.');
        }
    }

    public function obtenerPorTutoria($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM informes_avance
             WHERE id_tutoria = :id_tutoria
             ORDER BY numero_informe ASC"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetchAll();
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        $sql = "SELECT i.*, t.id_tutoria, t.estado AS estado_tutoria, t.tipo, m.nombre_materia,
                       es_usuario.nombre AS tutor_nombre, es_usuario.apellido AS tutor_apellido
                FROM informes_avance i
                INNER JOIN tutorias t ON i.id_tutoria = t.id_tutoria
                INNER JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
                INNER JOIN materias m ON t.id_materia = m.id_materia
                INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                INNER JOIN usuarios es_usuario ON tt.id_usuario = es_usuario.id_usuario
                WHERE te.id_estudiante = :id_estudiante
                ORDER BY i.fecha_registro DESC, i.numero_informe DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return $stmt->fetchAll();
    }

    public function porcentajeAcumulado($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT MAX(porcentaje_avance) AS porcentaje, COUNT(*) AS total_informes
             FROM informes_avance
             WHERE id_tutoria = :id_tutoria"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetch();
    }

    public function totalRegistrados($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS total FROM informes_avance WHERE id_tutoria = :id_tutoria"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return (int) $stmt->fetchColumn();
    }
}