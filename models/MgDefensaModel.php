<?php
class MgDefensaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Expedientes elegibles para programar defensa (sin defensa previa). */
    public function candidatos($idCohorte)
    {
        $sql = "SELECT e.id_expediente_mg, e.estado, e.solicitud_detalle,
                       est.registro_universitario,
                       u.nombre AS estudiante_nombre, u.apellido AS estudiante_apellido,
                       mg.nombre AS modalidad_nombre
                FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                JOIN modalidades_grado mg ON mg.id_modalidad_grado = e.id_modalidad_grado
                WHERE e.id_cohorte_mg = :cohorte
                  AND e.estado IN ('tutor_asignado', 'en_curso')
                  AND NOT EXISTS (SELECT 1 FROM defensas_mg d WHERE d.id_expediente_mg = e.id_expediente_mg)
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cohorte' => (int) $idCohorte]);
        return $stmt->fetchAll();
    }

    /** Docentes afines a la carrera del estudiante (mapeo docente_carreras). */
    public function docentesAfinCarrera($idEstudiante)
    {
        $sql = "SELECT DISTINCT tt.id_tutor, u.nombre, u.apellido, tt.especialidad
                FROM estudiantes est
                JOIN docente_carreras dc ON dc.id_carrera = est.id_carrera
                JOIN tutores tt ON tt.id_tutor = dc.id_tutor
                JOIN usuarios u ON u.id_usuario = tt.id_usuario
                WHERE est.id_estudiante = :est
                  AND u.estado = 'activo'
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':est' => (int) $idEstudiante]);
        return $stmt->fetchAll();
    }

    /** Listado de defensas con detalles para la agenda/cronograma. */
    public function listarPorCohorte($idCohorte, $estado = '')
    {
        $sql = "SELECT d.*, am.nombre AS ambiente_nombre,
                       e.estado AS estado_expediente, e.solicitud_detalle,
                       mg.nombre AS modalidad_nombre, c.nombre_periodo,
                       est.registro_universitario,
                       u.nombre AS estudiante_nombre, u.apellido AS estudiante_apellido,
                       ac.id_tutor AS id_tutor_activo,
                       ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido,
                       (SELECT COUNT(*) FROM tribunales_defensa_mg t
                         WHERE t.id_defensa_mg = d.id_defensa_mg) AS num_tribunales
                FROM defensas_mg d
                JOIN expedientes_mg e ON e.id_expediente_mg = d.id_expediente_mg
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                JOIN modalidades_grado mg ON mg.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = d.id_cohorte_mg
                LEFT JOIN ambientes_mg am ON am.id_ambiente_mg = d.id_ambiente_mg
                LEFT JOIN asignaciones_tutor ac ON ac.id_expediente_mg = e.id_expediente_mg
                                               AND ac.estado = 'activa'
                LEFT JOIN tutores tt ON tt.id_tutor = ac.id_tutor
                LEFT JOIN usuarios ut ON ut.id_usuario = tt.id_usuario
                WHERE d.id_cohorte_mg = :cohorte"
                . ($estado ? ' AND d.estado = :estado' : '') . "
                ORDER BY d.fecha_defensa ASC, d.hora_inicio ASC";
        $stmt = $this->pdo->prepare($sql);
        $params = [':cohorte' => (int) $idCohorte];
        if ($estado) {
            $params[':estado'] = $estado;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM defensas_mg WHERE id_defensa_mg = :id");
        $stmt->execute([':id' => (int) $id]);
        return $stmt->fetch();
    }

    public function tribunales($idDefensa)
    {
        $sql = "SELECT td.*, u.nombre, u.apellido, tt.especialidad
                FROM tribunales_defensa_mg td
                JOIN tutores tt ON tt.id_tutor = td.id_tutor
                JOIN usuarios u ON u.id_usuario = tt.id_usuario
                WHERE td.id_defensa_mg = :id
                ORDER BY td.id_tribunal_defensa_mg ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $idDefensa]);
        return $stmt->fetchAll();
    }

    /** Defensa completa (cabecera + expediente + tribunales). */
    public function obtenerConDetalle($id)
    {
        $sql = "SELECT d.*, am.nombre AS ambiente_nombre, am.ubicacion AS ambiente_ubicacion,
                       e.id_expediente_mg, e.estado AS estado_expediente, e.solicitud_detalle,
                       e.fecha_solicitud, e.id_estudiante, e.id_modalidad_grado,
                       mg.nombre AS modalidad_nombre, c.nombre_periodo,
                       est.registro_universitario, est.id_carrera,
                       ca.nombre_carrera,
                       u.nombre AS estudiante_nombre, u.apellido AS estudiante_apellido,
                       ac.id_tutor AS id_tutor_activo,
                       ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido
                FROM defensas_mg d
                JOIN expedientes_mg e ON e.id_expediente_mg = d.id_expediente_mg
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                JOIN carreras ca ON ca.id_carrera = est.id_carrera
                JOIN modalidades_grado mg ON mg.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = d.id_cohorte_mg
                LEFT JOIN ambientes_mg am ON am.id_ambiente_mg = d.id_ambiente_mg
                LEFT JOIN asignaciones_tutor ac ON ac.id_expediente_mg = e.id_expediente_mg
                                               AND ac.estado = 'activa'
                LEFT JOIN tutores tt ON tt.id_tutor = ac.id_tutor
                LEFT JOIN usuarios ut ON ut.id_usuario = tt.id_usuario
                WHERE d.id_defensa_mg = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $fila = $stmt->fetch();
        if (!$fila) {
            return null;
        }
        $fila['tribunales'] = $this->tribunales($id);

        return $fila;
    }

    public function crear($idExp, $idCohorte, $idAmbiente, $fecha, $hIni, $hFin, $obs, array $tutoresIds)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO defensas_mg (id_expediente_mg, id_cohorte_mg, id_ambiente_mg,
                                          fecha_defensa, hora_inicio, hora_fin, observaciones)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([(int) $idExp, (int) $idCohorte, $idAmbiente ? (int) $idAmbiente : null,
                            $fecha, $hIni, $hFin, $obs]);
            $idDefensa = (int) $this->pdo->lastInsertId();
            $this->guardarTribunales($idDefensa, $tutoresIds);
            $this->pdo->commit();

            return $idDefensa;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function actualizar($id, $idAmbiente, $fecha, $hIni, $hFin, $obs, array $tutoresIds)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE defensas_mg SET id_ambiente_mg = :amb, fecha_defensa = :fecha,
                       hora_inicio = :ini, hora_fin = :fin, observaciones = :obs
                 WHERE id_defensa_mg = :id"
            );
            $stmt->execute([':amb' => $idAmbiente ? (int) $idAmbiente : null, ':fecha' => $fecha,
                            ':ini' => $hIni, ':fin' => $hFin, ':obs' => $obs, ':id' => (int) $id]);
            $stmt = $this->pdo->prepare("DELETE FROM tribunales_defensa_mg WHERE id_defensa_mg = :id");
            $stmt->execute([':id' => (int) $id]);
            $this->guardarTribunales((int) $id, $tutoresIds);
            $this->pdo->commit();

            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function guardarTribunales($idDefensa, array $tutoresIds)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tribunales_defensa_mg (id_defensa_mg, id_tutor, rol)
             VALUES (:def, :tutor, :rol)"
        );
        $rol = 'presidente';
        foreach (array_unique(array_map('intval', $tutoresIds)) as $idTutor) {
            $stmt->execute([':def' => (int) $idDefensa, ':tutor' => $idTutor, ':rol' => $rol]);
            $rol = 'vocal';
        }
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM defensas_mg WHERE id_defensa_mg = :id");

        return $stmt->execute([':id' => (int) $id]);
    }

    /** Cruce de agenda: el ambiente ya está ocupado en esa fecha/hora. */
    public function conflictoAmbiente($idAmbiente, $fecha, $hIni, $hFin, $excluirId = 0)
    {
        if (!$idAmbiente) {
            return false;
        }
        $sql = "SELECT COUNT(*) FROM defensas_mg
                WHERE id_ambiente_mg = :amb AND fecha_defensa = :fecha AND id_defensa_mg <> :excluir
                  AND hora_inicio < :fin AND hora_fin > :ini";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':amb' => (int) $idAmbiente, ':fecha' => $fecha, ':excluir' => (int) $excluirId,
                        ':ini' => $hIni, ':fin' => $hFin]);

        return $stmt->fetchColumn() > 0;
    }

    /** Cruce de agenda: el docente (como tribunal o tutor activo) tiene otra defensa en el rango. */
    public function docenteOcupado($idTutor, $fecha, $hIni, $hFin, $excluirId = 0)
    {
        $sql = "SELECT COUNT(*) FROM defensas_mg d
                WHERE d.fecha_defensa = :fecha AND d.id_defensa_mg <> :excluir
                  AND d.hora_inicio < :fin AND d.hora_fin > :ini
                  AND (EXISTS (SELECT 1 FROM tribunales_defensa_mg t
                               WHERE t.id_defensa_mg = d.id_defensa_mg AND t.id_tutor = :tutor1)
                    OR EXISTS (SELECT 1 FROM expedientes_mg e
                               JOIN asignaciones_tutor a ON a.id_expediente_mg = e.id_expediente_mg
                                                         AND a.estado = 'activa'
                               WHERE e.id_expediente_mg = d.id_expediente_mg AND a.id_tutor = :tutor2))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':fecha' => $fecha, ':excluir' => (int) $excluirId,
                        ':ini' => $hIni, ':fin' => $hFin, ':tutor1' => (int) $idTutor, ':tutor2' => (int) $idTutor]);

        return $stmt->fetchColumn() > 0;
    }

    /** Registra las notas del tribunal y calcula promedio + resultado. */
    public function evaluar($idDefensa, array $notasPorTribunal, $notaAprob)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE tribunales_defensa_mg SET nota = :nota WHERE id_tribunal_defensa_mg = :id"
            );
            $total = 0.0;
            $n = 0;
            foreach ($notasPorTribunal as $idTrib => $nota) {
                $stmt->execute([':nota' => (float) $nota, ':id' => (int) $idTrib]);
                $total += (float) $nota;
                $n++;
            }
            $promedio = $n > 0 ? round($total / $n, 2) : 0.0;
            $resultado = $promedio >= (float) $notaAprob ? 'aprobado' : 'reprobado';
            $stmt = $this->pdo->prepare(
                "UPDATE defensas_mg SET nota_final = :prom, resultado = :res, estado = 'evaluada'
                 WHERE id_defensa_mg = :id"
            );
            $stmt->execute([':prom' => $promedio, ':res' => $resultado, ':id' => (int) $idDefensa]);
            $this->pdo->commit();

            return ['promedio' => $promedio, 'resultado' => $resultado];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Sincroniza el estado final del expediente tras la defensa. */
    public function actualizarEstadoExpediente($idDefensa, $resultado, $resolucion)
    {
        $estado = ($resultado === 'aprobado') ? 'concluido' : 'cerrado';
        $sql = "UPDATE expedientes_mg e
                JOIN defensas_mg d ON d.id_expediente_mg = e.id_expediente_mg
                SET e.estado = :estado, e.resolucion_admin = :resolucion
                WHERE d.id_defensa_mg = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':estado' => $estado, ':resolucion' => $resolucion, ':id' => (int) $idDefensa]);
    }

    public function asignarCorrelativo($idDefensa, $correlativo)
    {
        $stmt = $this->pdo->prepare("UPDATE defensas_mg SET correlativo = :corr WHERE id_defensa_mg = :id");

        return $stmt->execute([':corr' => $correlativo, ':id' => (int) $idDefensa]);
    }

    public function tieneDefensa($idExp): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM defensas_mg WHERE id_expediente_mg = :id");
        $stmt->execute([':id' => (int) $idExp]);

        return $stmt->fetchColumn() > 0;
    }

    /** Estudiante dueño de un expediente. */
    public function estudianteDeExpediente($idExp): int
    {
        $stmt = $this->pdo->prepare("SELECT id_estudiante FROM expedientes_mg WHERE id_expediente_mg = :id");
        $stmt->execute([':id' => (int) $idExp]);

        return (int) $stmt->fetchColumn();
    }

    /** Carrera del estudiante dueño de un expediente. */
    public function expedienteCarrera($idExp): int
    {
        $sql = "SELECT est.id_carrera FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                WHERE e.id_expediente_mg = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $idExp]);

        return (int) $stmt->fetchColumn();
    }

    /** Tutor activo de un expediente (o null). */
    public function tutorActivoDe($idExp): ?int
    {
        $sql = "SELECT ac.id_tutor FROM asignaciones_tutor ac
                WHERE ac.id_expediente_mg = :id AND ac.estado = 'activa' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $idExp]);
        $tutor = $stmt->fetchColumn();

        return $tutor ? (int) $tutor : null;
    }

    /**
     * Defensas de un estudiante con ambiente, cohorte, tribunal y nota final.
     * Se cruza por expediente porque defensas_mg tiene UNIQUE en
     * id_expediente_mg: un estudiante tiene como maximo una defensa por
     * expediente, pero puede tener varias a lo largo de distintas cohortes.
     */
    public function obtenerPorEstudiante($idEstudiante)
    {
        $sql = "SELECT d.id_defensa_mg, d.correlativo, d.fecha_defensa, d.hora_inicio,
                       d.hora_fin, d.estado, d.nota_final, d.resultado, d.observaciones,
                       e.id_expediente_mg, e.estado AS estado_expediente,
                       m.nombre AS modalidad_nombre,
                       c.nombre_periodo AS cohorte,
                       am.nombre AS ambiente_nombre, am.ubicacion AS ambiente_ubicacion,
                       (SELECT COUNT(*) FROM tribunales_defensa_mg t
                         WHERE t.id_defensa_mg = d.id_defensa_mg) AS num_tribunales,
                       (SELECT GROUP_CONCAT(CONCAT(u.nombre, ' ', u.apellido) ORDER BY t.id_tribunal_defensa_mg SEPARATOR ', ')
                          FROM tribunales_defensa_mg t
                          JOIN tutores tt ON tt.id_tutor = t.id_tutor
                          JOIN usuarios u ON u.id_usuario = tt.id_usuario
                         WHERE t.id_defensa_mg = d.id_defensa_mg) AS tribunal_nombres
                FROM defensas_mg d
                JOIN expedientes_mg e ON e.id_expediente_mg = d.id_expediente_mg
                JOIN modalidades_grado m ON m.id_modalidad_grado = e.id_modalidad_grado
                JOIN cohortes_mg c ON c.id_cohorte_mg = d.id_cohorte_mg
                LEFT JOIN ambientes_mg am ON am.id_ambiente_mg = d.id_ambiente_mg
                WHERE e.id_estudiante = :estudiante
                ORDER BY d.fecha_defensa DESC, d.hora_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estudiante' => (int) $idEstudiante]);
        return $stmt->fetchAll();
    }
}