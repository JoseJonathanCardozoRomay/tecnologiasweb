<?php
require_once __DIR__ . '/ConfiguracionModel.php';

class TutoriaModel
{
    private const ESTADOS = [
        'pendiente', 'asignada', 'aceptada', 'en_proceso',
        'en_reasignacion', 'realizada', 'cancelada', 'finalizada',
        'confirmada',
    ];

    public static function estadosPermitidos(): array
    {
        return [
            'pendiente'      => 'Pendiente',
            'asignada'       => 'Asignada',
            'aceptada'       => 'Aceptada',
            'en_proceso'     => 'En proceso',
            'en_reasignacion'=> 'En reasignación',
            'realizada'      => 'Realizada',
            'cancelada'      => 'Cancelada',
            'finalizada'     => 'Finalizada',
            'confirmada'     => 'Confirmada',
        ];
    }

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function crearSolicitud($id_estudiante, $id_tutor, $id_materia, $fecha, $hora_inicio, $hora_fin, $modalidad, $tipo = 'apoyo', $id_modalidad = null, $observaciones = null)
    {
        if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
            throw new InvalidArgumentException("La modalidad debe ser 'presencial' u 'virtual'.");
        }

        if (!in_array($tipo, ['apoyo', 'grado'], true)) {
            throw new InvalidArgumentException("El tipo debe ser 'apoyo' o 'grado'.");
        }

        if ($tipo === 'grado' && empty($id_modalidad)) {
            throw new InvalidArgumentException('Debes seleccionar la modalidad de graduación.');
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
            $this->pdo->beginTransaction();

            $insert = $this->pdo->prepare(
                "INSERT INTO tutorias
                    (id_estudiante, id_tutor, id_materia, id_modalidad, tipo, fecha, hora_inicio, hora_fin, modalidad, estado, observaciones)
                 VALUES
                    (:id_estudiante, :id_tutor, :id_materia, :id_modalidad, :tipo, :fecha, :hora_inicio, :hora_fin, :modalidad, 'pendiente', :observaciones)"
            );
            $insert->execute([
                ':id_estudiante' => $id_estudiante,
                ':id_tutor'      => $id_tutor,
                ':id_materia'    => $id_materia,
                ':id_modalidad'  => $tipo === 'grado' ? $id_modalidad : null,
                ':tipo'          => $tipo,
                ':fecha'         => $fecha,
                ':hora_inicio'   => $hora_inicio,
                ':hora_fin'      => $hora_fin,
                ':modalidad'     => $modalidad,
                ':observaciones' => $observaciones,
            ]);

            $id_tutoria = (int) $this->pdo->lastInsertId();
            if ($tipo === 'apoyo') {
                $this->inscribirEstudiante($id_tutoria, $id_estudiante);
            }

            $this->pdo->commit();
            return $id_tutoria;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al crear la solicitud de tutoría.');
        }
    }

    public function inscribirEstudiante($id_tutoria, $id_estudiante)
    {
        try {
            $existe = $this->pdo->prepare(
                "SELECT COUNT(*) FROM tutoria_estudiantes WHERE id_tutoria = :id_tutoria AND id_estudiante = :id_estudiante"
            );
            $existe->execute([
                ':id_tutoria'   => $id_tutoria,
                ':id_estudiante' => $id_estudiante,
            ]);

            if ((int) $existe->fetchColumn() > 0) {
                return false;
            }

            $tutoria = $this->obtenerPorId($id_tutoria);
            if (!$tutoria || $tutoria['tipo'] !== 'apoyo') {
                throw new InvalidArgumentException('Solo las tutorías de apoyo permiten varios estudiantes.');
            }
            if (!in_array($tutoria['estado'], ['pendiente', 'confirmada', 'en_proceso'], true)) {
                throw new InvalidArgumentException('La tutoría ya no acepta inscripciones.');
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO tutoria_estudiantes (id_tutoria, id_estudiante) VALUES (:id_tutoria, :id_estudiante)"
            );
            $stmt->execute([
                ':id_tutoria'   => $id_tutoria,
                ':id_estudiante' => $id_estudiante,
            ]);

            $this->verificarHabilitacionApoyo($id_tutoria);

            return true;
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al inscribir al estudiante en la tutoría.');
        }
    }

    public function verificarHabilitacionApoyo($id_tutoria)
    {
        $tutoria = $this->obtenerPorId($id_tutoria);
        if (!$tutoria || $tutoria['tipo'] !== 'apoyo' || $tutoria['estado'] !== 'pendiente') {
            return false;
        }

        $minimo = (int) (new ConfiguracionModel($this->pdo))->int('cupo_minimo_apoyo', 3);
        $inscritos = $this->contarEstudiantes($id_tutoria);

        if ($inscritos >= $minimo) {
            return $this->cambiarEstado($id_tutoria, 'confirmada');
        }

        return false;
    }

    public function contarEstudiantes($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tutoria_estudiantes WHERE id_tutoria = :id_tutoria"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return (int) $stmt->fetchColumn();
    }

    public function obtenerEstudiantesInscritos($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT e.id_estudiante, e.id_usuario, u.nombre, u.apellido, u.correo, te.fecha_inscripcion
             FROM tutoria_estudiantes te
             INNER JOIN estudiantes e ON te.id_estudiante = e.id_estudiante
             INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
             WHERE te.id_tutoria = :id_tutoria
             ORDER BY te.fecha_inscripcion ASC"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerApoyoAbiertas($id_materia = null, $id_carrera = null)
    {
        $sql = "SELECT t.id_tutoria, t.id_tutor, t.id_materia, t.fecha, t.hora_inicio, t.hora_fin,
                       t.modalidad, t.lugar_o_enlace, t.estado, t.observaciones,
                       tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                       tt.especialidad, m.nombre_materia
                FROM tutorias t
                INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                INNER JOIN materias m ON t.id_materia = m.id_materia
                WHERE t.tipo = 'apoyo'
                  AND t.estado IN ('pendiente', 'confirmada')";
        $params = [];

        if ($id_materia !== null) {
            $sql .= " AND t.id_materia = :id_materia";
            $params[':id_materia'] = $id_materia;
        }
        if ($id_carrera !== null) {
            $sql .= " AND m.id_carrera = :id_carrera";
            $params[':id_carrera'] = $id_carrera;
        }

        $sql .= " ORDER BY t.fecha ASC, t.hora_inicio ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tutorias as &$tutoria) {
            $tutoria['inscritos'] = $this->contarEstudiantes($tutoria['id_tutoria']);
            $this->normalizarTutoria($tutoria);
        }
        unset($tutoria);

        return $tutorias;
    }

    public function obtenerSolicitudesGradoPendientes()
    {
        $sql = "SELECT t.id_tutoria, t.id_estudiante, t.id_tutor, t.id_materia, t.id_modalidad,
                       t.fecha, t.hora_inicio, t.hora_fin, t.modalidad, t.estado, t.observaciones,
                       est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                       tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                       m.nombre_materia, md.nombre AS modalidad_nombre
                FROM tutorias t
                INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
                INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
                INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                INNER JOIN materias m ON t.id_materia = m.id_materia
                LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                WHERE t.tipo = 'grado' AND t.estado = 'pendiente'
                ORDER BY t.fecha_solicitud DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function cambiarEstado($id_tutoria, $nuevo_estado, $id_estado_conclusion = null)
    {
        if (!in_array($nuevo_estado, self::ESTADOS, true)) {
            throw new InvalidArgumentException(
                "Estado inválido. Estados permitidos: " . implode(', ', self::ESTADOS) . "."
            );
        }

        try {
            if ($nuevo_estado === 'finalizada') {
                if (!ctype_digit((string) $id_estado_conclusion)) {
                    throw new InvalidArgumentException(
                        'Para finalizar la Modalidad de Grado debes indicar su resultado: Aprobada, Reprobada, Abandono u Otros.'
                    );
                }

                $existe = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM estados_conclusion_mg WHERE id_estado_conclusion = :id"
                );
                $existe->execute([':id' => (int) $id_estado_conclusion]);

                if ((int) $existe->fetchColumn() === 0) {
                    throw new InvalidArgumentException('El estado de conclusión seleccionado no es válido.');
                }
            }

            $stmt = $this->pdo->prepare(
                "UPDATE tutorias
                    SET estado = :estado,
                        id_estado_conclusion = :id_estado_conclusion
                  WHERE id_tutoria = :id_tutoria"
            );

            return $stmt->execute([
                ':estado'              => $nuevo_estado,
                ':id_estado_conclusion'=> $nuevo_estado === 'finalizada' ? (int) $id_estado_conclusion : null,
                ':id_tutoria'          => $id_tutoria,
            ]);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al actualizar el estado de la tutoría.');
        }
    }

    public function obtenerEstadosConclusion()
    {
        $stmt = $this->pdo->query(
            "SELECT id_estado_conclusion, nombre, descripcion
               FROM estados_conclusion_mg
              ORDER BY id_estado_conclusion"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar($id_estudiante, $id_tutor, $id_materia, $id_modalidad, $fecha, $hora_inicio, $hora_fin, $modalidad, $lugar_o_enlace = null, $observaciones = null, $tipo = 'grado')
    {
        if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
            throw new InvalidArgumentException("La modalidad debe ser 'presencial' u 'virtual'.");
        }

        if (empty($id_modalidad) && $tipo === 'grado') {
            throw new InvalidArgumentException('Debes seleccionar la modalidad de graduación.');
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
            $this->pdo->beginTransaction();

            $pendiente = $this->solicitudGradoPendienteDelEstudiante($id_estudiante);

            if ($pendiente) {
                $update = $this->pdo->prepare(
                    "UPDATE tutorias
                     SET id_tutor = :id_tutor, id_materia = :id_materia, id_modalidad = :id_modalidad,
                         fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin,
                         modalidad = :modalidad, lugar_o_enlace = :lugar_o_enlace,
                         observaciones = :observaciones, estado = 'asignada'
                     WHERE id_tutoria = :id_tutoria"
                );
                $update->execute([
                    ':id_tutor'       => $id_tutor,
                    ':id_materia'     => $id_materia,
                    ':id_modalidad'   => $id_modalidad,
                    ':fecha'          => $fecha,
                    ':hora_inicio'    => $hora_inicio,
                    ':hora_fin'       => $hora_fin,
                    ':modalidad'      => $modalidad,
                    ':lugar_o_enlace' => $lugar_o_enlace,
                    ':observaciones'  => $observaciones,
                    ':id_tutoria'     => (int) $pendiente['id_tutoria'],
                ]);

                $this->pdo->commit();
                return (int) $pendiente['id_tutoria'];
            }

            $insert = $this->pdo->prepare(
                "INSERT INTO tutorias
                    (id_estudiante, id_tutor, id_materia, id_modalidad, tipo, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado, observaciones)
                 VALUES
                    (:id_estudiante, :id_tutor, :id_materia, :id_modalidad, :tipo, :fecha, :hora_inicio, :hora_fin, :modalidad, :lugar_o_enlace, 'asignada', :observaciones)"
            );
            $insert->execute([
                ':id_estudiante' => $id_estudiante,
                ':id_tutor'      => $id_tutor,
                ':id_materia'    => $id_materia,
                ':id_modalidad'  => $id_modalidad,
                ':tipo'          => $tipo,
                ':fecha'         => $fecha,
                ':hora_inicio'   => $hora_inicio,
                ':hora_fin'      => $hora_fin,
                ':modalidad'     => $modalidad,
                ':lugar_o_enlace'=> $lugar_o_enlace,
                ':observaciones' => $observaciones,
            ]);

            $id_tutoria = (int) $this->pdo->lastInsertId();
            if ($tipo === 'apoyo') {
                $this->inscribirEstudiante($id_tutoria, $id_estudiante);
            }

            $this->pdo->commit();
            return $id_tutoria;
        } catch (InvalidArgumentException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al asignar la tutoría.');
        }
    }

    private function solicitudGradoPendienteDelEstudiante($id_estudiante)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_tutoria FROM tutorias
             WHERE id_estudiante = :id_estudiante AND tipo = 'grado' AND estado = 'pendiente'
             ORDER BY fecha_solicitud DESC LIMIT 1"
        );
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        $id = $stmt->fetchColumn();
        if (!$id) {
            return null;
        }

        return ['id_tutoria' => (int) $id];
    }

    public function obtenerPorId($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, ec.nombre AS estado_conclusion_nombre
               FROM tutorias t
               LEFT JOIN estados_conclusion_mg ec ON t.id_estado_conclusion = ec.id_estado_conclusion
              WHERE t.id_tutoria = :id"
        );
        $stmt->execute([':id' => $id_tutoria]);
        $tutoria = $stmt->fetch();

        if ($tutoria) {
            $tutoria['inscritos'] = $this->contarEstudiantes($id_tutoria);
        }

        return $tutoria;
    }

    public function obtenerMetricasPorTutor($id_tutor)
    {
        return $this->contarPorEstado("WHERE id_tutor = :id_tutor", [':id_tutor' => $id_tutor]);
    }

    public function obtenerMetricasPorEstudiante($id_estudiante)
    {
        return $this->contarPorEstado("WHERE id_estudiante = :id_estudiante", [':id_estudiante' => $id_estudiante]);
    }

    public function obtenerMetricasGlobales()
    {
        return $this->contarPorEstado("WHERE 1 = 1", []);
    }

    private function contarPorEstado($where, array $params)
    {
        $sql = "SELECT estado, COUNT(*) AS total FROM tutorias $where GROUP BY estado";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $base = [
            'total'      => 0,
            'pendiente'  => 0,
            'confirmada' => 0,
            'realizada'  => 0,
            'cancelada'  => 0,
        ];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (array_key_exists($fila['estado'], $base)) {
                $base[$fila['estado']] = (int) $fila['total'];
            }
            $base['total'] += (int) $fila['total'];
        }

        return $base;
    }

    public function mgCompletada($id_estudiante)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tutorias
                    WHERE id_estudiante = :id_estudiante
                      AND tipo = 'grado'
                      AND estado = 'finalizada'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_estudiante' => $id_estudiante]);

            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar el estado de la Modalidad de Grado.');
        }
    }

    public function mgConcluida($id_estudiante)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT t.id_tutoria, t.estado, ec.nombre AS estado_conclusion_nombre
                   FROM tutorias t
                   LEFT JOIN estados_conclusion_mg ec ON t.id_estado_conclusion = ec.id_estado_conclusion
                  WHERE t.id_estudiante = :id_estudiante
                    AND t.tipo = 'grado'
                    AND t.estado = 'finalizada'
                  ORDER BY t.fecha DESC
                  LIMIT 1"
            );
            $stmt->execute([':id_estudiante' => $id_estudiante]);
            $conclusion = $stmt->fetch(PDO::FETCH_ASSOC);

            return $conclusion ?: null;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar la conclusión de la Modalidad de Grado.');
        }
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        try {
            $sql = "SELECT t.id_tutoria, t.id_estudiante, t.id_tutor, t.id_materia,
                           t.fecha, t.hora_inicio, t.hora_fin, t.modalidad,
                           t.lugar_o_enlace, t.estado, t.observaciones, t.fecha_solicitud,
                           t.id_modalidad, t.tipo, t.id_estado_conclusion,
                           ec.nombre AS estado_conclusion_nombre,
                           md.nombre AS modalidad_nombre,
                           tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                           tt.especialidad, m.nombre_materia
                    FROM tutorias t
                    INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                    INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    LEFT JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
                    LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                    LEFT JOIN estados_conclusion_mg ec ON t.id_estado_conclusion = ec.id_estado_conclusion
                    WHERE t.id_estudiante = :id_propia
                       OR te.id_estudiante = :id_inscrita
                    GROUP BY t.id_tutoria
                    ORDER BY t.fecha DESC, t.hora_inicio DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_propia'   => $id_estudiante,
                ':id_inscrita' => $id_estudiante,
            ]);
            $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tutorias as &$tutoria) {
                $tutoria['inscritos'] = $this->contarEstudiantes($tutoria['id_tutoria']);
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
                           t.id_modalidad, t.tipo, t.id_estado_conclusion,
                           ec.nombre AS estado_conclusion_nombre,
                           md.nombre AS modalidad_nombre,
                           est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                           m.nombre_materia
                    FROM tutorias t
                    INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
                    INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                    LEFT JOIN estados_conclusion_mg ec ON t.id_estado_conclusion = ec.id_estado_conclusion
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
                $tutoria['inscritos'] = $this->contarEstudiantes($tutoria['id_tutoria']);
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
                           t.id_modalidad, t.tipo, t.id_estado_conclusion,
                           ec.nombre AS estado_conclusion_nombre,
                           md.nombre AS modalidad_nombre,
                           est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                           tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                           m.nombre_materia
                    FROM tutorias t
                    INNER JOIN estudiantes es ON t.id_estudiante = es.id_estudiante
                    INNER JOIN usuarios est ON es.id_usuario = est.id_usuario
                    INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                    INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                    INNER JOIN materias m ON t.id_materia = m.id_materia
                    LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                    LEFT JOIN estados_conclusion_mg ec ON t.id_estado_conclusion = ec.id_estado_conclusion
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
                $tutoria['inscritos'] = $this->contarEstudiantes($tutoria['id_tutoria']);
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

        if (($tutoria['tipo'] ?? 'apoyo') === 'apoyo') {
            $minimo = (int) (new ConfiguracionModel($this->pdo))->int('cupo_minimo_apoyo', 3);
            $tutoria['minimo_cupos'] = $minimo;
            $tutoria['habilitada'] = ((int) ($tutoria['inscritos'] ?? 0) >= $minimo) || (($tutoria['estado'] ?? '') !== 'pendiente');
        }
    }
}