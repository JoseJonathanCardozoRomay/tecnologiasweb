<?php
class TutoriaModel
{
    private const ESTADOS = ['confirmada', 'realizada', 'cancelada'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function crearSolicitud($id_estudiante, $id_tutor, $id_materia, $fecha, $hora_inicio, $hora_fin, $modalidad, $observaciones = null)
    {
        if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
            throw new InvalidArgumentException("La modalidad debe ser 'presencial' u 'virtual'.");
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) AS total
                 FROM tutorias
                 WHERE id_tutor = :id_tutor
                   AND fecha = :fecha
                   AND estado != 'cancelada'
                   AND hora_inicio < :hora_fin
                   AND hora_fin > :hora_inicio"
            );
            $stmt->execute([
                ':id_tutor'    => $id_tutor,
                ':fecha'       => $fecha,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin'    => $hora_fin,
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                throw new InvalidArgumentException('El tutor ya tiene una tutoría en ese horario. Elegí otra hora.');
            }
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al validar la disponibilidad del tutor.');
        }

        try {
            $insert = $this->pdo->prepare(
                "INSERT INTO tutorias
                    (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, estado, observaciones)
                 VALUES
                    (:id_estudiante, :id_tutor, :id_materia, :fecha, :hora_inicio, :hora_fin, :modalidad, 'pendiente', :observaciones)"
            );
            $insert->execute([
                ':id_estudiante' => $id_estudiante,
                ':id_tutor'      => $id_tutor,
                ':id_materia'    => $id_materia,
                ':fecha'         => $fecha,
                ':hora_inicio'   => $hora_inicio,
                ':hora_fin'      => $hora_fin,
                ':modalidad'     => $modalidad,
                ':observaciones' => $observaciones,
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Error al crear la solicitud de tutoría.');
        }
    }

    public function cambiarEstado($id_tutoria, $nuevo_estado)
    {
        if (!in_array($nuevo_estado, self::ESTADOS, true)) {
            throw new InvalidArgumentException(
                "Estado inválido. Estados permitidos: " . implode(', ', self::ESTADOS) . "."
            );
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE tutorias SET estado = :estado WHERE id_tutoria = :id_tutoria"
            );

            return $stmt->execute([
                ':estado'     => $nuevo_estado,
                ':id_tutoria' => $id_tutoria,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Error al actualizar el estado de la tutoría.');
        }
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        try {
            $sql = "SELECT t.id_tutoria, t.id_estudiante, t.id_tutor, t.id_materia,
                           t.fecha, t.hora_inicio, t.hora_fin, t.modalidad,
                           t.lugar_o_enlace, t.estado, t.observaciones, t.fecha_solicitud,
                           tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                           tt.especialidad, m.nombre_materia
                    FROM tutorias t
                    INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                    INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    WHERE t.id_estudiante = :id_estudiante
                    ORDER BY t.fecha DESC, t.hora_inicio DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_estudiante' => $id_estudiante]);
            $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tutorias as &$tutoria) {
                $this->normalizarTutoria($tutoria);
            }
            unset($tutoria);

            return $tutorias;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar las tutorías del estudiante.');
        }
    }

    public function obtenerPorTutor($id_tutor, $estado = null)
    {
        try {
            $sql = "SELECT t.id_tutoria, t.id_estudiante, t.id_tutor, t.id_materia,
                           t.fecha, t.hora_inicio, t.hora_fin, t.modalidad,
                           t.lugar_o_enlace, t.estado, t.observaciones, t.fecha_solicitud,
                           est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                           m.nombre_materia
                    FROM tutorias t
                    INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
                    INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    WHERE t.id_tutor = :id_tutor";

            $params = [':id_tutor' => $id_tutor];

            if ($estado !== null) {
                $sql .= " AND t.estado = :estado";
                $params[':estado'] = $estado;
            }

            $sql .= " ORDER BY t.fecha DESC, t.hora_inicio DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tutorias as &$tutoria) {
                $this->normalizarTutoria($tutoria);
            }
            unset($tutoria);

            return $tutorias;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar las tutorías del tutor.');
        }
    }

    public function obtenerTodas($estado = null, $fecha_desde = null, $fecha_hasta = null)
    {
        try {
            $sql = "SELECT t.id_tutoria, t.id_estudiante, t.id_tutor, t.id_materia,
                           t.fecha, t.hora_inicio, t.hora_fin, t.modalidad,
                           t.lugar_o_enlace, t.estado, t.observaciones, t.fecha_solicitud,
                           est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                           tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                           m.nombre_materia
                    FROM tutorias t
                    INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
                    INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
                    INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                    INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    WHERE 1 = 1";

            $params = [];

            if ($estado !== null) {
                $sql .= " AND t.estado = :estado";
                $params[':estado'] = $estado;
            }

            if ($fecha_desde !== null) {
                $sql .= " AND t.fecha >= :fecha_desde";
                $params[':fecha_desde'] = $fecha_desde;
            }

            if ($fecha_hasta !== null) {
                $sql .= " AND t.fecha <= :fecha_hasta";
                $params[':fecha_hasta'] = $fecha_hasta;
            }

            $sql .= " ORDER BY t.fecha DESC, t.hora_inicio DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tutorias as &$tutoria) {
                $this->normalizarTutoria($tutoria);
            }
            unset($tutoria);

            return $tutorias;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar las tutorías.');
        }
    }

    private function normalizarTutoria(array &$tutoria)
    {
        if (array_key_exists('fecha', $tutoria) && $tutoria['fecha'] !== null) {
            $tutoria['fecha'] = substr($tutoria['fecha'], 0, 10) . ' 00:00:00';
        }

        foreach (['hora_inicio', 'hora_fin'] as $campo) {
            if (isset($tutoria[$campo]) && $tutoria[$campo] !== null) {
                $tutoria[$campo] = '1970-01-01 ' . substr($tutoria[$campo], 0, 8);
            }
        }
    }
}