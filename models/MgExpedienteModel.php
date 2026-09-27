<?php
class MgExpedienteModel
{
    const ESTADOS = ['solicitado', 'validado', 'tutor_asignado', 'en_curso', 'concluido', 'cerrado'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT e.id_expediente_mg, e.estado, e.fecha_solicitud,
                       e.id_estudiante, e.id_modalidad_grado, e.id_cohorte_mg,
                       CONCAT(u.nombre, ' ', u.apellido) AS estudiante,
                       u.usuario AS usuario_estudiante,
                       m.nombre AS modalidad,
                       m.requiere_tutor AS modalidad_requiere_tutor,
                       c.nombre_periodo AS cohorte,
                       (SELECT CONCAT(tu.nombre, ' ', tu.apellido)
                        FROM asignaciones_tutor a
                        JOIN tutores t ON t.id_tutor = a.id_tutor
                        JOIN usuarios tu ON tu.id_usuario = t.id_usuario
                        WHERE a.id_expediente_mg = e.id_expediente_mg AND a.estado = 'activa'
                        LIMIT 1) AS tutor_asignado
                FROM expedientes_mg e
                JOIN estudiantes es ON es.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = es.id_usuario
                JOIN modalidades_grado m ON m.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                ORDER BY e.fecha_solicitud DESC, e.id_expediente_mg DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT e.*,
                       es.id_carrera AS estudiante_carrera_id,
                       es.semestre AS estudiante_semestre,
                       es.materias_completadas AS estudiante_materias,
                       es.registro_universitario AS estudiante_ru,
                       es.acceso_mg_desbloqueado AS estudiante_acceso,
                       CONCAT(u.nombre, ' ', u.apellido) AS estudiante,
                       u.correo AS estudiante_correo,
                       ca.nombre_carrera AS carrera,
                       m.nombre AS modalidad,
                       m.requiere_tutor AS modalidad_requiere_tutor,
                       m.flujo AS modalidad_flujo,
                       c.nombre_periodo AS cohorte,
                       c.estado AS cohorte_estado,
                       CONCAT(v.nombre, ' ', v.apellido) AS validador_nombre
                FROM expedientes_mg e
                JOIN estudiantes es ON es.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = es.id_usuario
                JOIN carreras ca ON ca.id_carrera = es.id_carrera
                JOIN modalidades_grado m ON m.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                LEFT JOIN usuarios v ON v.id_usuario = e.validado_por
                WHERE e.id_expediente_mg = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($idEstudiante, $idModalidad, $idCohorte, $detalle)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO expedientes_mg (id_estudiante, id_modalidad_grado, id_cohorte_mg, solicitud_detalle)
             VALUES (:estudiante, :modalidad, :cohorte, :detalle)"
        );
        $stmt->execute([
            ':estudiante' => $idEstudiante,
            ':modalidad' => $idModalidad,
            ':cohorte' => $idCohorte,
            ':detalle' => trim($detalle),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function esEstadoValido($estado)
    {
        return in_array($estado, self::ESTADOS, true);
    }

    public function cambiarEstado($id, $estado, $validadoPor = null)
    {
        $query = "UPDATE expedientes_mg SET estado = :estado";
        $params = [':estado' => $estado, ':id' => $id];
        if ($estado === 'validado' && $validadoPor !== null) {
            $query .= ", validado_por = :validado_por, fecha_validacion = NOW()";
            $params[':validado_por'] = $validadoPor;
        }
        if ($estado === 'solicitado') {
            $query .= ", validado_por = NULL, fecha_validacion = NULL";
        }
        $query .= " WHERE id_expediente_mg = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    public function actualizarResolucion($id, $resolucion)
    {
        $stmt = $this->pdo->prepare("UPDATE expedientes_mg SET resolucion_admin = :resolucion WHERE id_expediente_mg = :id");
        return $stmt->execute([
            ':id' => $id,
            ':resolucion' => trim($resolucion),
        ]);
    }

    public function estudiantesDisponibles()
    {
        $sql = "SELECT es.id_estudiante, es.id_carrera,
                       CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo,
                       u.usuario,
                       es.registro_universitario,
                       ca.nombre_carrera, es.semestre, es.materias_completadas
                FROM estudiantes es
                JOIN usuarios u ON u.id_usuario = es.id_usuario
                JOIN carreras ca ON ca.id_carrera = es.id_carrera
                WHERE es.acceso_mg_desbloqueado = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM expedientes_mg e
                      WHERE e.id_estudiante = es.id_estudiante
                        AND e.estado NOT IN ('cerrado', 'concluido'))
                ORDER BY u.apellido ASC, u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function tutoresAfines($idExpediente)
    {
        $expediente = $this->obtenerPorId($idExpediente);
        if (!$expediente) {
            return [];
        }
        $idCarrera = $expediente['estudiante_carrera_id'];

        $sql = "SELECT t.id_tutor,
                       CONCAT(u.nombre, ' ', u.apellido) AS tutor_nombre,
                       u.correo AS tutor_correo,
                       t.especialidad AS tutor_especialidad,
                       IFNULL(GROUP_CONCAT(DISTINCT mm.nombre_materia ORDER BY mm.nombre_materia SEPARATOR ', '), '') AS materias_afines,
                       COUNT(DISTINCT mm.id_materia) AS afinidad,
                       (SELECT COUNT(*) FROM asignaciones_tutor a2
                        WHERE a2.id_tutor = t.id_tutor AND a2.estado = 'activa') AS carga_actual
                FROM tutores t
                JOIN usuarios u ON u.id_usuario = t.id_usuario
                LEFT JOIN tutor_materia tm ON tm.id_tutor = t.id_tutor
                LEFT JOIN materias mm ON mm.id_materia = tm.id_materia AND mm.id_carrera = :carrera
                WHERE t.id_tutor IN (
                    SELECT tm3.id_tutor
                    FROM tutor_materia tm3
                    JOIN materias m3 ON m3.id_materia = tm3.id_materia AND m3.id_carrera = :carrera2
                )
                GROUP BY t.id_tutor, u.nombre, u.apellido, u.correo, t.especialidad
                ORDER BY afinidad DESC, tutor_nombre ASC, t.id_tutor ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':carrera' => $idCarrera,
            ':carrera2' => $idCarrera,
        ]);
        return $stmt->fetchAll();
    }

    public function esTutorAfin($idExpediente, $idTutor)
    {
        foreach ($this->tutoresAfines($idExpediente) as $tutor) {
            if ((int) $tutor['id_tutor'] === (int) $idTutor) {
                return true;
            }
        }
        return false;
    }

    public function asignarTutor($idExpediente, $idTutor, $asignadoPor, $observaciones)
    {
        $this->pdo->beginTransaction();
        try {
            $cierra = $this->pdo->prepare(
                "UPDATE asignaciones_tutor SET estado = 'finalizada'
                 WHERE id_expediente_mg = :expediente AND estado = 'activa'"
            );
            $cierra->execute([':expediente' => $idExpediente]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO asignaciones_tutor (id_expediente_mg, id_tutor, asignado_por, estado, observaciones)
                 VALUES (:expediente, :tutor, :asignado_por, 'activa', :observaciones)"
            );
            $stmt->execute([
                ':expediente' => $idExpediente,
                ':tutor' => $idTutor,
                ':asignado_por' => $asignadoPor,
                ':observaciones' => trim($observaciones),
            ]);

            $nuevoId = (int) $this->pdo->lastInsertId();

            $estado = $this->pdo->prepare(
                "UPDATE expedientes_mg SET estado = 'tutor_asignado' WHERE id_expediente_mg = :id AND estado IN ('solicitado', 'validado', 'en_curso')"
            );
            $estado->execute([':id' => $idExpediente]);

            $this->pdo->commit();
            return $nuevoId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function finalizarAsignacion($idAsignacion)
    {
        $stmt = $this->pdo->prepare("UPDATE asignaciones_tutor SET estado = 'finalizada' WHERE id_asignacion_tutor = :id AND estado = 'activa'");
        return $stmt->execute([':id' => $idAsignacion]);
    }

    public function historial($idExpediente)
    {
        $sql = "SELECT a.id_asignacion_tutor, a.fecha_asignacion, a.estado, a.observaciones,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS tutor_nombre,
                       t.especialidad AS tutor_especialidad,
                       CONCAT(op.nombre, ' ', op.apellido) AS operador_nombre
                FROM asignaciones_tutor a
                JOIN tutores t ON t.id_tutor = a.id_tutor
                JOIN usuarios tu ON tu.id_usuario = t.id_usuario
                JOIN usuarios op ON op.id_usuario = a.asignado_por
                WHERE a.id_expediente_mg = :expediente
                ORDER BY a.fecha_asignacion DESC, a.id_asignacion_tutor DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':expediente' => $idExpediente]);
        return $stmt->fetchAll();
    }

    public function asignacionActiva($idExpediente)
    {
        $sql = "SELECT a.*,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS tutor_nombre,
                       t.especialidad AS tutor_especialidad,
                       tu.correo AS tutor_correo
                FROM asignaciones_tutor a
                JOIN tutores t ON t.id_tutor = a.id_tutor
                JOIN usuarios tu ON tu.id_usuario = t.id_usuario
                WHERE a.id_expediente_mg = :expediente AND a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':expediente' => $idExpediente]);
        return $stmt->fetch();
    }

    /**
     * Todos los expedientes de un estudiante, del mas reciente al mas antiguo.
     * El que esta en curso se marca con 'es_activo' para destacarlo en el portal.
     */
    public function obtenerPorEstudiante($idEstudiante)
    {
        $sql = "SELECT e.id_expediente_mg, e.estado, e.fecha_solicitud, e.solicitud_detalle,
                       e.resolucion_admin, e.fecha_validacion,
                       e.estado NOT IN ('cerrado', 'concluido') AS es_activo,
                       m.nombre AS modalidad, m.flujo AS modalidad_flujo,
                       c.id_cohorte_mg, c.nombre_periodo AS cohorte,
                       c.fecha_inicio AS cohorte_inicio, c.fecha_fin AS cohorte_fin,
                       c.estado AS cohorte_estado,
                       (SELECT CONCAT(tu.nombre, ' ', tu.apellido)
                          FROM asignaciones_tutor a
                          JOIN tutores t ON t.id_tutor = a.id_tutor
                          JOIN usuarios tu ON tu.id_usuario = t.id_usuario
                         WHERE a.id_expediente_mg = e.id_expediente_mg
                           AND a.estado = 'activa'
                         LIMIT 1) AS tutor_asignado
                FROM expedientes_mg e
                JOIN modalidades_grado m ON m.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                WHERE e.id_estudiante = :estudiante
                ORDER BY (e.estado NOT IN ('cerrado', 'concluido')) DESC,
                         e.fecha_solicitud DESC, e.id_expediente_mg DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estudiante' => (int) $idEstudiante]);
        return $stmt->fetchAll();
    }

    /**
     * Expediente activo del estudiante (portal MG).
     * Mismo criterio de 'activo' que estudiantesDisponibles(): todo estado
     * salvo los terminales 'cerrado' y 'concluido'. Desempata por fecha de
     * solicitud, igual que obtenerTodas().
     */
    public function obtenerActivoPorEstudiante($idEstudiante)
    {
        $sql = "SELECT e.id_expediente_mg, e.estado, e.fecha_solicitud, e.solicitud_detalle,
                       e.resolucion_admin, e.fecha_validacion, e.id_cohorte_mg,
                       es.id_carrera, es.registro_universitario, es.semestre,
                       es.materias_completadas, es.acceso_mg_desbloqueado,
                       ca.nombre_carrera,
                       m.nombre AS modalidad, m.flujo AS modalidad_flujo,
                       m.requiere_tutor AS modalidad_requiere_tutor,
                       c.nombre_periodo AS cohorte, c.estado AS cohorte_estado,
                       c.fecha_inicio AS cohorte_inicio, c.fecha_fin AS cohorte_fin
                FROM expedientes_mg e
                JOIN estudiantes es ON es.id_estudiante = e.id_estudiante
                JOIN carreras ca ON ca.id_carrera = es.id_carrera
                JOIN modalidades_grado m ON m.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                WHERE e.id_estudiante = :estudiante
                  AND e.estado NOT IN ('cerrado', 'concluido')
                ORDER BY e.fecha_solicitud DESC, e.id_expediente_mg DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estudiante' => (int) $idEstudiante]);
        return $stmt->fetch() ?: null;
    }
}