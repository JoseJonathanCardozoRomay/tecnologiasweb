<?php
class TutoriaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas($filtro_estado = null, $filtro_periodo = null)
    {
        $sql = "SELECT tu.*,
                       ue.nombre AS est_nombre, ue.apellido AS est_apellido, ue.correo AS est_correo,
                       ut.nombre AS tut_nombre, ut.apellido AS tut_apellido, ut.correo AS tut_correo,
                       m.nombre_materia, c.nombre_carrera,
                       ev.calificacion, ev.comentario AS ev_comentario
                FROM tutorias tu
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria";

        $params = [];
        $condiciones = [];
        if (!empty($filtro_estado)) {
            $condiciones[] = "tu.estado = :estado";
            $params[':estado'] = $filtro_estado;
        }
        if (!empty($filtro_periodo)) {
            $condiciones[] = "tu.periodo = :periodo";
            $params[':periodo'] = $filtro_periodo;
        }
        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(' AND ', $condiciones);
        }

        $sql .= " ORDER BY tu.fecha DESC, tu.hora_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id_tutoria)
    {
        $sql = "SELECT tu.*,
                       ue.nombre AS est_nombre, ue.apellido AS est_apellido, ue.correo AS est_correo, ue.telefono AS est_telefono,
                       ut.nombre AS tut_nombre, ut.apellido AS tut_apellido, ut.correo AS tut_correo, ut.telefono AS tut_telefono,
                       m.nombre_materia, c.nombre_carrera,
                       ev.calificacion, ev.comentario AS ev_comentario
                FROM tutorias tu
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria
                WHERE tu.id_tutoria = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_tutoria]);
        return $stmt->fetch();
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        $sql = "SELECT tu.*,
                       ut.nombre AS tut_nombre, ut.apellido AS tut_apellido, ut.correo AS tut_correo,
                       m.nombre_materia, c.nombre_carrera,
                       ev.calificacion, ev.comentario AS ev_comentario
                FROM tutorias tu
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria
                WHERE tu.id_estudiante = :id_est
                ORDER BY tu.fecha DESC, tu.hora_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_est' => $id_estudiante]);
        return $stmt->fetchAll();
    }

    public function obtenerPorTutor($id_tutor)
    {
        $sql = "SELECT tu.*,
                       ue.nombre AS est_nombre, ue.apellido AS est_apellido, ue.correo AS est_correo,
                       m.nombre_materia, c.nombre_carrera,
                       ev.calificacion, ev.comentario AS ev_comentario
                FROM tutorias tu
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria
                WHERE tu.id_tutor = :id_tutor
                ORDER BY tu.fecha DESC, tu.hora_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetchAll();
    }

    public function crear($datos)
    {
        $sql = "INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, fecha, periodo, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado, observaciones)
            VALUES (:id_estudiante, :id_tutor, :id_materia, :fecha, :periodo, :hora_inicio, :hora_fin, :modalidad, :lugar_o_enlace, 'pendiente', :observaciones)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_estudiante'   => $datos['id_estudiante'],
            ':id_tutor'        => $datos['id_tutor'],
            ':id_materia'      => $datos['id_materia'],
            ':fecha'           => $datos['fecha'],
            ':periodo'         => trim($datos['periodo'] ?? 'I-' . date('Y')),
            ':hora_inicio'     => $datos['hora_inicio'],
            ':hora_fin'        => $datos['hora_fin'],
            ':modalidad'       => $datos['modalidad'] ?? 'presencial',
            ':lugar_o_enlace'  => trim($datos['lugar_o_enlace'] ?? ''),
            ':observaciones'   => trim($datos['observaciones'] ?? '')
        ]);
    }

    public function actualizarEstado($id_tutoria, $nuevo_estado, $observaciones = null)
    {
        if ($observaciones !== null) {
            $sql = "UPDATE tutorias SET estado = :estado, observaciones = :obs WHERE id_tutoria = :id";
            $params = [
                ':estado' => $nuevo_estado,
                ':obs'    => trim($observaciones),
                ':id'     => $id_tutoria
            ];
        } else {
            $sql = "UPDATE tutorias SET estado = :estado WHERE id_tutoria = :id";
            $params = [
                ':estado' => $nuevo_estado,
                ':id'     => $id_tutoria
            ];
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function eliminar($id_tutoria)
    {
        $stmt = $this->pdo->prepare("DELETE FROM tutorias WHERE id_tutoria = :id");
        return $stmt->execute([':id' => $id_tutoria]);
    }

    // Métricas para paneles de control
    public function obtenerMetricasGlobales($periodo = null)
    {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) AS confirmadas,
                    SUM(CASE WHEN estado = 'realizada' THEN 1 ELSE 0 END) AS realizadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) AS canceladas
                FROM tutorias";
        $params = [];
        if (!empty($periodo)) {
            $sql .= " WHERE periodo = :periodo";
            $params[':periodo'] = $periodo;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function obtenerPeriodosDisponibles()
    {
        return $this->pdo->query("SELECT DISTINCT periodo FROM tutorias ORDER BY periodo DESC")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obtenerReportePorPeriodo($periodo)
    {
        $metricas = $this->obtenerMetricasGlobales($periodo);
        $params = [':periodo' => $periodo];

        $stmt = $this->pdo->prepare("SELECT m.nombre_materia, COUNT(*) AS total_sesiones,
                                            SUM(tu.estado = 'realizada') AS realizadas
                                     FROM tutorias tu
                                     INNER JOIN materias m ON tu.id_materia = m.id_materia
                                     WHERE tu.periodo = :periodo
                                     GROUP BY tu.id_materia, m.nombre_materia
                                     ORDER BY total_sesiones DESC, m.nombre_materia");
        $stmt->execute($params);
        $materias = $stmt->fetchAll();

        $stmt = $this->pdo->prepare("SELECT CONCAT(u.nombre, ' ', u.apellido) AS tutor,
                                            COUNT(CASE WHEN tu.estado = 'realizada' THEN 1 END) AS sesiones_realizadas,
                                            COALESCE(SUM(CASE WHEN tu.estado = 'realizada' THEN TIME_TO_SEC(TIMEDIFF(tu.hora_fin, tu.hora_inicio)) / 3600 ELSE 0 END), 0) AS horas_dictadas
                                     FROM tutorias tu
                                     INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                                     WHERE tu.periodo = :periodo
                                     GROUP BY tu.id_tutor, u.nombre, u.apellido
                                     ORDER BY horas_dictadas DESC, tutor");
        $stmt->execute($params);
        $tutores = $stmt->fetchAll();

        $stmt = $this->pdo->prepare("SELECT ROUND(AVG(ev.calificacion), 2) AS promedio_satisfaccion,
                                            COUNT(ev.id_evaluacion) AS evaluaciones
                                     FROM evaluaciones_tutoria ev
                                     INNER JOIN tutorias tu ON ev.id_tutoria = tu.id_tutoria
                                     WHERE tu.periodo = :periodo");
        $stmt->execute($params);
        $satisfaccion = $stmt->fetch();

        return compact('metricas', 'materias', 'tutores', 'satisfaccion');
    }
}
