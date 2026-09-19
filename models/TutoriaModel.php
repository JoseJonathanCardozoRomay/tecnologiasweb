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

    public function obtenerTodasOrdenadas($filtro_estado, $filtro_periodo, $orden, $dir)
    {
        $columnas = [
            'fecha' => 'tu.fecha',
            'materia' => 'm.nombre_materia',
            'estudiante' => 'ue.nombre',
            'tutor' => 'ut.nombre',
            'estado' => 'tu.estado',
        ];
        $orden = $columnas[$orden] ?? $columnas['fecha'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
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
            $condiciones[] = 'tu.estado = :estado';
            $params[':estado'] = $filtro_estado;
        }
        if (!empty($filtro_periodo)) {
            $condiciones[] = 'tu.periodo = :periodo';
            $params[':periodo'] = $filtro_periodo;
        }
        if (!empty($condiciones)) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $sql .= " ORDER BY {$orden} {$dir}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPaginadas($q, $filtro_estado, $filtro_periodo, $orden, $dir, $limite, $offset)
    {
        $columnas = ['fecha' => 'tu.fecha', 'materia' => 'm.nombre_materia', 'estudiante' => 'ue.nombre', 'tutor' => 'ut.nombre', 'estado' => 'tu.estado'];
        $ordenSql = $columnas[$orden] ?? $columnas['fecha'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT tu.*, ue.nombre AS est_nombre, ue.apellido AS est_apellido, ue.correo AS est_correo,
                       ut.nombre AS tut_nombre, ut.apellido AS tut_apellido, ut.correo AS tut_correo,
                       m.nombre_materia, c.nombre_carrera, ev.calificacion, ev.comentario AS ev_comentario
                FROM tutorias tu INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria";
        $condiciones = [];
        $params = [];
        if ($filtro_estado !== null) { $condiciones[] = 'tu.estado = :estado'; $params[':estado'] = $filtro_estado; }
        if ($filtro_periodo !== '') { $condiciones[] = 'tu.periodo = :periodo'; $params[':periodo'] = $filtro_periodo; }
        if ($q !== '') {
            $condiciones[] = "CONCAT_WS(' ', ue.nombre, ue.apellido, ut.nombre, ut.apellido, m.nombre_materia, c.nombre_carrera, tu.periodo, tu.estado) LIKE :q ESCAPE '\\\\'";
            $params[':q'] = valorBusquedaLike($q);
        }
        if ($condiciones) $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        $sql .= " ORDER BY {$ordenSql} {$dirSql} LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $clave => $valor) $stmt->bindValue($clave, $valor, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($q = '', $filtro_estado = null, $filtro_periodo = '')
    {
        $sql = "SELECT COUNT(*) FROM tutorias tu
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera";
        $condiciones = [];
        $params = [];
        if ($filtro_estado !== null) { $condiciones[] = 'tu.estado = :estado'; $params[':estado'] = $filtro_estado; }
        if ($filtro_periodo !== '') { $condiciones[] = 'tu.periodo = :periodo'; $params[':periodo'] = $filtro_periodo; }
        if ($q !== '') { $condiciones[] = "CONCAT_WS(' ', ue.nombre, ue.apellido, ut.nombre, ut.apellido, m.nombre_materia, c.nombre_carrera, tu.periodo, tu.estado) LIKE :q ESCAPE '\\\\'"; $params[':q'] = valorBusquedaLike($q); }
        if ($condiciones) $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $clave => $valor) $stmt->bindValue($clave, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id_tutoria)
    {
        $sql = "SELECT tu.*,
                       e.id_usuario AS estudiante_id_usuario,
                       t.id_usuario AS tutor_id_usuario,
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

    public function perteneceATutor($id_tutoria, $id_usuario)
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tutorias tu INNER JOIN tutores t ON tu.id_tutor = t.id_tutor WHERE tu.id_tutoria = :id AND t.id_usuario = :usuario LIMIT 1');
        $stmt->execute([':id' => $id_tutoria, ':usuario' => $id_usuario]);
        return (bool) $stmt->fetchColumn();
    }

    public function perteneceAEstudiante($id_tutoria, $id_usuario)
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tutorias tu INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante WHERE tu.id_tutoria = :id AND e.id_usuario = :usuario LIMIT 1');
        $stmt->execute([':id' => $id_tutoria, ':usuario' => $id_usuario]);
        return (bool) $stmt->fetchColumn();
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

    public function obtenerPorEstudiantePaginadas($id_estudiante, $limite, $offset)
    {
        $sql = "SELECT tu.*, ut.nombre AS tut_nombre, ut.apellido AS tut_apellido, ut.correo AS tut_correo,
                       m.nombre_materia, c.nombre_carrera, ev.calificacion, ev.comentario AS ev_comentario,
                       ss.asistio, ss.temas_tratados, ss.avance, ss.recomendaciones
                FROM tutorias tu INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios ut ON t.id_usuario = ut.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria
                LEFT JOIN seguimiento_sesion ss ON tu.id_tutoria = ss.id_tutoria
                WHERE tu.id_estudiante = :id_est ORDER BY tu.fecha DESC, tu.hora_inicio DESC LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_est', $id_estudiante, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorEstudiante($id_estudiante)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tutorias WHERE id_estudiante = :id_est');
        $stmt->execute([':id_est' => $id_estudiante]);
        return (int) $stmt->fetchColumn();
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

    public function obtenerPorTutorPaginadas($id_tutor, $limite, $offset)
    {
        $sql = "SELECT tu.*, ue.nombre AS est_nombre, ue.apellido AS est_apellido, ue.correo AS est_correo,
                       m.nombre_materia, c.nombre_carrera, ev.calificacion, ev.comentario AS ev_comentario,
                       ss.asistio, ss.temas_tratados, ss.avance, ss.recomendaciones
                FROM tutorias tu INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN evaluaciones_tutoria ev ON tu.id_tutoria = ev.id_tutoria
                LEFT JOIN seguimiento_sesion ss ON tu.id_tutoria = ss.id_tutoria
                WHERE tu.id_tutor = :id_tutor ORDER BY tu.fecha DESC, tu.hora_inicio DESC LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorTutor($id_tutor)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tutorias WHERE id_tutor = :id_tutor');
        $stmt->execute([':id_tutor' => $id_tutor]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerMetricasPorTutor($id_tutor)
    {
        $stmt = $this->pdo->prepare("SELECT
            SUM(estado = 'pendiente') AS pendientes,
            SUM(estado = 'confirmada') AS confirmadas,
            SUM(estado = 'realizada') AS realizadas
            FROM tutorias WHERE id_tutor = :id_tutor");
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetch();
    }

    public function obtenerMetricasPorEstudiante($id_estudiante)
    {
        $stmt = $this->pdo->prepare("SELECT
            SUM(estado = 'pendiente') AS pendientes,
            SUM(estado = 'confirmada') AS confirmadas,
            SUM(estado = 'realizada') AS realizadas
            FROM tutorias WHERE id_estudiante = :id_estudiante");
        $stmt->execute([':id_estudiante' => $id_estudiante]);
        return $stmt->fetch();
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

    public function hayCruceTutor($idTutor, $fecha, $ini, $fin, $excluirId = null)
    {
        $sql = "SELECT 1 FROM tutorias WHERE id_tutor = :id AND fecha = :fecha AND estado IN ('pendiente','confirmada') AND hora_inicio < :fin AND hora_fin > :ini";
        $params = [':id' => $idTutor, ':fecha' => $fecha, ':ini' => $ini, ':fin' => $fin];
        if ($excluirId !== null) { $sql .= ' AND id_tutoria <> :excluir'; $params[':excluir'] = $excluirId; }
        $stmt = $this->pdo->prepare($sql); $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function hayCruceEstudiante($idEstudiante, $fecha, $ini, $fin, $excluirId = null)
    {
        $sql = "SELECT 1 FROM tutorias WHERE id_estudiante = :id AND fecha = :fecha AND estado IN ('pendiente','confirmada') AND hora_inicio < :fin AND hora_fin > :ini";
        $params = [':id' => $idEstudiante, ':fecha' => $fecha, ':ini' => $ini, ':fin' => $fin];
        if ($excluirId !== null) { $sql .= ' AND id_tutoria <> :excluir'; $params[':excluir'] = $excluirId; }
        $stmt = $this->pdo->prepare($sql); $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function contarActivasPorEstudiante($idEstudiante)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tutorias WHERE id_estudiante = :id AND estado IN ('pendiente','confirmada') AND fecha >= CURDATE()");
        $stmt->execute([':id' => $idEstudiante]);
        return (int) $stmt->fetchColumn();
    }

    public function crearSinCruces($datos)
    {
        try {
            $this->pdo->beginTransaction();
            $bloqueo = $this->pdo->prepare('SELECT id_tutor FROM tutores WHERE id_tutor = :id FOR UPDATE');
            $bloqueo->execute([':id' => $datos['id_tutor']]);
            if (!$bloqueo->fetchColumn()) { $this->pdo->rollBack(); return ['ok' => false, 'error' => 'El tutor seleccionado no existe.']; }
            if ($this->hayCruceTutor($datos['id_tutor'], $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin'])) { $this->pdo->rollBack(); return ['ok' => false, 'error' => 'El tutor ya tiene una tutoría en ese horario.']; }
            if ($this->hayCruceEstudiante($datos['id_estudiante'], $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin'])) { $this->pdo->rollBack(); return ['ok' => false, 'error' => 'El estudiante ya tiene una tutoría en ese horario.']; }
            if ($this->contarActivasPorEstudiante($datos['id_estudiante']) >= TUTORIA_MAX_ACTIVAS) { $this->pdo->rollBack(); return ['ok' => false, 'error' => 'El estudiante alcanzó el máximo de ' . TUTORIA_MAX_ACTIVAS . ' tutorías activas.']; }
            $this->crear($datos);
            $this->pdo->commit();
            return ['ok' => true, 'error' => null];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log($e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo agendar la sesión.'];
        }
    }

    public function actualizarEstado($id_tutoria, $nuevo_estado, $observaciones = null, $motivoCancelacion = null)
    {
        $sets = ['estado = :estado'];
        $params = [
            ':estado' => $nuevo_estado,
            ':id'     => $id_tutoria
        ];
        if ($observaciones !== null) {
            $sets[] = 'observaciones = :obs';
            $params[':obs'] = trim($observaciones);
        }
        if ($motivoCancelacion !== null) {
            $sets[] = 'motivo_cancelacion = :motivo';
            $params[':motivo'] = trim($motivoCancelacion);
        }
        $sql = "UPDATE tutorias SET " . implode(', ', $sets) . " WHERE id_tutoria = :id";
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

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total_seguimientos,
                                            SUM(ss.asistio = 'si') AS asistencias
                                     FROM seguimiento_sesion ss
                                     INNER JOIN tutorias tu ON ss.id_tutoria = tu.id_tutoria
                                     WHERE tu.periodo = :periodo");
        $stmt->execute($params);
        $asistencia = $stmt->fetch();
        $totalSeguimientos = (int) ($asistencia['total_seguimientos'] ?? 0);
        $asistencias = (int) ($asistencia['asistencias'] ?? 0);
        $porcentajeAsistencia = $totalSeguimientos > 0 ? round(($asistencias / $totalSeguimientos) * 100) : null;
        $asistencia['porcentaje_asistencia'] = $porcentajeAsistencia;

        return compact('metricas', 'materias', 'tutores', 'satisfaccion', 'asistencia');
    }
}
