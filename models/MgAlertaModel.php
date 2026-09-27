<?php
class MgAlertaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Calcula las alertas A1-A9 por demanda.
     * Devuelve un arreglo de alertas con: codigo, nivel, titulo, mensaje, referencias.
     */
    public function calcular(array $params): array
    {
        $alertas = [];
        $dias = max(1, (int) ($params['dias_anticipacion_tribunales'] ?? 7));
        $recomendada = (int) ($params['tutor_carga_recomendada'] ?? 8);
        $maxima = (int) ($params['tutor_carga_maxima'] ?? 10);
        $minTribunales = (int) ($params['tribunales_min_mg'] ?? 2);

        // A1 - Expedientes válidos sin asignación activa de tutor
        $refs = $this->expedientesSinTutor(["e.estado IN ('validado','tutor_asignado','en_curso')"], '');
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A1', 'nivel' => 'danger',
                'titulo' => 'Expedientes sin tutor asignado',
                'mensaje' => count($refs) . ' expediente(s) en estado válido sin una asignación activa de tutor.',
                'referencias' => $refs,
            ];
        }

        // A2 - Tutores con sobrecarga (sobre la carga recomendada)
        $refs = $this->tutoresPorCarga($recomendada, false, false);
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A2', 'nivel' => 'warning',
                'titulo' => 'Tutores sobre la carga recomendada',
                'mensaje' => count($refs) . ' tutor(es) con más asignaciones activas que la carga recomendada (' . $recomendada . ').',
                'referencias' => $refs,
            ];
        }

        // A3 - Tutores con carga máxima alcanzada
        $refs = $this->tutoresPorCarga($maxima, true, false);
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A3', 'nivel' => 'danger',
                'titulo' => 'Tutores con carga máxima alcanzada',
                'mensaje' => count($refs) . ' tutor(es) alcanzaron la carga máxima (' . $maxima . ' asignaciones activas).',
                'referencias' => $refs,
            ];
        }

        // A4 - Defensas próximas con tribunales incompletos
        $refs = $this->defensasIncompletas($dias, $minTribunales);
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A4', 'nivel' => 'danger',
                'titulo' => 'Defensas próximas sin tribunales completos',
                'mensaje' => count($refs) . ' defensa(s) dentro de los próximos ' . $dias . ' días con menos de ' . $minTribunales . ' tribunal(es).',
                'referencias' => $refs,
            ];
        }

        // A5 - Defensas vencidas sin evaluar
        $refs = $this->defensasSinEvaluar();
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A5', 'nivel' => 'warning',
                'titulo' => 'Defensas vencidas sin evaluar',
                'mensaje' => count($refs) . ' defensa(s) con fecha anterior a hoy siguen sin evaluación.',
                'referencias' => $refs,
            ];
        }

        // A6 - Expedientes validados sin tutor en más de 3 días
        $refs = $this->expedientesSinTutor(["e.estado = 'validado'", "e.fecha_validacion <= CURDATE() - INTERVAL 3 DAY"], 'validado');
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A6', 'nivel' => 'warning',
                'titulo' => 'Expedientes validados sin tutor (3+ días)',
                'mensaje' => count($refs) . ' expediente(s) validados hace más de 3 días sin asignación de tutor.',
                'referencias' => $refs,
            ];
        }

        // A7 - Estudiantes elegibles (acceso desbloqueado) sin expediente en cohorte vigente
        $refs = $this->estudiantesElegiblesSinExpediente();
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A7', 'nivel' => 'info',
                'titulo' => 'Estudiantes elegibles sin expediente',
                'mensaje' => count($refs) . ' estudiante(s) con acceso desbloqueado sin solicitud en una cohorte abierta o planificada.',
                'referencias' => $refs,
            ];
        }

        // A8 - Expedientes en curso cuya asignación de tutor ya finalizó
        $refs = $this->expedientesEnCursoSinTutorActivo();
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A8', 'nivel' => 'warning',
                'titulo' => 'Expedientes en curso sin tutor activo',
                'mensaje' => count($refs) . ' expediente(s) en curso sin una asignación de tutor activa.',
                'referencias' => $refs,
            ];
        }

        // A9 - Solicitudes sin validar hace más de 7 días
        $refs = $this->solicitudesSinValidar();
        if ($refs) {
            $alertas[] = [
                'codigo' => 'A9', 'nivel' => 'info',
                'titulo' => 'Solicitudes sin validar (7+ días)',
                'mensaje' => count($refs) . ' solicitud(es) en estado "solicitado" hace más de 7 días sin validar.',
                'referencias' => $refs,
            ];
        }

        return $alertas;
    }

    public function atender($codigo, $titulo, $mensaje, $idReferencia, $idUsuario, $nota): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO alertas_mg (codigo, titulo, mensaje, id_referencia, atendida, atendida_por, nota, fecha_atencion)
             VALUES (:codigo, :titulo, :mensaje, :referencia, 1, :por, :nota, NOW())"
        );

        return $stmt->execute([
            ':codigo' => $codigo,
            ':titulo' => $titulo,
            ':mensaje' => $mensaje,
            ':referencia' => $idReferencia ? (int) $idReferencia : null,
            ':por' => (int) $idUsuario,
            ':nota' => $nota,
        ]);
    }

    public function historial($limite = 60): array
    {
        $sql = "SELECT a.*, u.nombre AS atendido_por_nombre, u.apellido AS atendido_por_apellido
                FROM alertas_mg a
                LEFT JOIN usuarios u ON u.id_usuario = a.atendida_por
                ORDER BY a.fecha_atencion DESC, a.id_alerta_mg DESC
                LIMIT " . (int) $limite;
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    private function expedientesSinTutor(array $condiciones, $sufijo): array
    {
        $sql = "SELECT e.id_expediente_mg, u.apellido, u.nombre, e.estado, c.nombre_periodo
                FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                WHERE " . implode(' AND ', $condiciones) . "
                  AND NOT EXISTS (SELECT 1 FROM asignaciones_tutor a
                                  WHERE a.id_expediente_mg = e.id_expediente_mg AND a.estado = 'activa')
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    private function tutoresPorCarga($limite, bool $maxima, bool $_unused): array
    {
        $sql = "SELECT t.id_tutor, u.nombre, u.apellido, COUNT(a.id_asignacion_tutor) AS activas
                FROM tutores t
                JOIN usuarios u ON u.id_usuario = t.id_usuario
                JOIN asignaciones_tutor a ON a.id_tutor = t.id_tutor AND a.estado = 'activa'
                WHERE u.estado = 'activo'
                GROUP BY t.id_tutor, u.nombre, u.apellido
                HAVING activas " . ($maxima ? '>=' : '>') . " :limite
                ORDER BY activas DESC, u.apellido, u.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':limite' => (int) $limite]);

        return $stmt->fetchAll();
    }

    private function defensasIncompletas($dias, $minTribunales): array
    {
        $sql = "SELECT d.id_defensa_mg, d.fecha_defensa, d.hora_inicio,
                       u.apellido, u.nombre, am.nombre AS ambiente_nombre,
                       (SELECT COUNT(*) FROM tribunales_defensa_mg t
                         WHERE t.id_defensa_mg = d.id_defensa_mg) AS num_tribunales
                FROM defensas_mg d
                JOIN expedientes_mg e ON e.id_expediente_mg = d.id_expediente_mg
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                LEFT JOIN ambientes_mg am ON am.id_ambiente_mg = d.id_ambiente_mg
                WHERE d.estado = 'programada'
                  AND d.fecha_defensa BETWEEN CURDATE()
                      AND CURDATE() + INTERVAL :dias DAY
                HAVING num_tribunales < :min
                ORDER BY d.fecha_defensa, d.hora_inicio";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dias' => (int) $dias, ':min' => (int) $minTribunales]);

        return $stmt->fetchAll();
    }

    private function defensasSinEvaluar(): array
    {
        $sql = "SELECT d.id_defensa_mg, d.fecha_defensa, d.hora_inicio, u.apellido, u.nombre
                FROM defensas_mg d
                JOIN expedientes_mg e ON e.id_expediente_mg = d.id_expediente_mg
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                WHERE d.estado = 'programada' AND d.fecha_defensa <= CURDATE()
                ORDER BY d.fecha_defensa, d.hora_inicio";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    private function estudiantesElegiblesSinExpediente(): array
    {
        $sql = "SELECT est.id_estudiante, u.apellido, u.nombre, est.registro_universitario
                FROM estudiantes est
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                WHERE est.acceso_mg_desbloqueado = 1
                  AND NOT EXISTS (
                    SELECT 1 FROM expedientes_mg e
                    JOIN cohortes_mg c ON c.id_cohorte_mg = e.id_cohorte_mg
                    WHERE e.id_estudiante = est.id_estudiante
                      AND c.estado IN ('abierta', 'planificada')
                  )
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    private function expedientesEnCursoSinTutorActivo(): array
    {
        $sql = "SELECT e.id_expediente_mg, u.apellido, u.nombre
                FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                WHERE e.estado = 'en_curso'
                  AND NOT EXISTS (SELECT 1 FROM asignaciones_tutor a
                                  WHERE a.id_expediente_mg = e.id_expediente_mg AND a.estado = 'activa')
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    private function solicitudesSinValidar(): array
    {
        $sql = "SELECT e.id_expediente_mg, u.apellido, u.nombre, e.fecha_solicitud
                FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                WHERE e.estado = 'solicitado'
                  AND e.fecha_solicitud <= CURDATE() - INTERVAL 7 DAY
                ORDER BY e.fecha_solicitud";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }
}