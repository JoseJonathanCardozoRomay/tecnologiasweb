<?php
class ReunionModel
{
    private const ASISTENCIAS = ['si', 'no', 'tardanza'];
    private const CUMPLIMIENTOS = ['completo', 'parcial', 'pendiente'];
    private const EXTENSIONES_PERMITIDAS = ['jpg', 'jpeg', 'png', 'pdf'];
    private const MIMES_PERMITIDOS = ['image/jpeg', 'image/png', 'application/pdf'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_tutoria, $fecha, $hora_inicio, $hora_fin, $lugar_o_enlace = null, $observaciones = null, $evidencia_url = null)
    {
        if ($hora_fin <= $hora_inicio) {
            throw new InvalidArgumentException('La hora de fin debe ser posterior a la hora de inicio.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO reuniones
                    (id_tutoria, fecha, hora_inicio, hora_fin, lugar_o_enlace, asistio_estudiante, evidencia_url, observaciones)
                 VALUES
                    (:id_tutoria, :fecha, :hora_inicio, :hora_fin, :lugar_o_enlace, 'si', :evidencia_url, :observaciones)"
            );
            $stmt->execute([
                ':id_tutoria'          => $id_tutoria,
                ':fecha'               => $fecha,
                ':hora_inicio'         => $hora_inicio,
                ':hora_fin'            => $hora_fin,
                ':lugar_o_enlace'      => $lugar_o_enlace,
                ':evidencia_url'       => $evidencia_url,
                ':observaciones'       => $observaciones,
            ]);

            $id_reunion = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();

            return $id_reunion;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            throw new RuntimeException('Error al registrar la reunión.');
        }
    }

    public function registrarSeguimiento($id_reunion, $id_usuario, $asistencia, $cumplimiento, $observaciones = null, $compromisos = null, $evidencia_url = null)
    {
        if (!in_array($asistencia, self::ASISTENCIAS, true)) {
            throw new InvalidArgumentException("La asistencia debe ser 'si', 'no' o 'tardanza'.");
        }
        if (!in_array($cumplimiento, self::CUMPLIMIENTOS, true)) {
            throw new InvalidArgumentException("El cumplimiento debe ser 'completo', 'parcial' o 'pendiente'.");
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO reunion_seguimientos
                    (id_reunion, id_usuario_registro, asistencia, cumplimiento, observaciones, compromisos)
                 VALUES
                    (:id_reunion, :id_usuario, :asistencia, :cumplimiento, :observaciones, :compromisos)
                 ON DUPLICATE KEY UPDATE
                    asistencia = VALUES(asistencia),
                    cumplimiento = VALUES(cumplimiento),
                    observaciones = VALUES(observaciones),
                    compromisos = VALUES(compromisos),
                    fecha_actualizacion = CURRENT_TIMESTAMP"
            );
            $stmt->execute([
                ':id_reunion'      => (int) $id_reunion,
                ':id_usuario'      => (int) $id_usuario,
                ':asistencia'      => $asistencia,
                ':cumplimiento'    => $cumplimiento,
                ':observaciones'   => $observaciones,
                ':compromisos'     => $compromisos,
            ]);

            $updateParams = [':asistencia' => $asistencia, ':id_reunion' => (int) $id_reunion];
            $sql = "UPDATE reuniones SET asistio_estudiante = :asistencia";
            if ($evidencia_url !== null) {
                $sql .= ", evidencia_url = :evidencia_url";
                $updateParams[':evidencia_url'] = $evidencia_url;
            }
            $sql .= " WHERE id_reunion = :id_reunion";
            $sincroniza = $this->pdo->prepare($sql);
            $sincroniza->execute($updateParams);

            $this->pdo->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            throw new RuntimeException('Error al guardar el seguimiento de la reunión.');
        }
    }

    public function guardarInforme($id_reunion, $ruta_informe)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE reuniones SET informe_url = :ruta WHERE id_reunion = :id_reunion"
        );

        return $stmt->execute([
            ':ruta'       => $ruta_informe,
            ':id_reunion' => (int) $id_reunion,
        ]);
    }

    public function obtenerSeguimiento($id_reunion)
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, us.nombre AS nombre_registro, us.apellido AS apellido_registro
             FROM reunion_seguimientos s
             LEFT JOIN usuarios us ON us.id_usuario = s.id_usuario_registro
             WHERE s.id_reunion = :id_reunion
             LIMIT 1"
        );
        $stmt->execute([':id_reunion' => (int) $id_reunion]);

        return $stmt->fetch();
    }

    public function seguimientosPorReuniones(array $ids_reuniones)
    {
        $ids = array_values(array_filter(array_map('intval', $ids_reuniones)));
        if (empty($ids)) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT s.*, us.nombre AS nombre_registro, us.apellido AS apellido_registro
             FROM reunion_seguimientos s
             LEFT JOIN usuarios us ON us.id_usuario = s.id_usuario_registro
             WHERE s.id_reunion IN ($marcadores)"
        );
        $stmt->execute($ids);

        $porReunion = [];
        foreach ($stmt->fetchAll() as $fila) {
            $porReunion[(int) $fila['id_reunion']] = $fila;
        }

        return $porReunion;
    }

    public function obtenerPorTutoria($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, t.estado AS estado_tutoria
             FROM reuniones r
             INNER JOIN tutorias t ON r.id_tutoria = t.id_tutoria
             WHERE r.id_tutoria = :id_tutoria
             ORDER BY r.fecha DESC, r.hora_inicio DESC"
        );
        $stmt->execute([':id_tutoria' => (int) $id_tutoria]);

        return $stmt->fetchAll();
    }

    public function obtenerPorTutor($id_tutor, $id_tutoria = null)
    {
        $sql = "SELECT r.*, t.estado AS estado_tutoria, t.tipo AS tipo_tutoria,
                       m.nombre_materia,
                       est.estudiantes_nombres,
                       est.cantidad_estudiantes
                FROM reuniones r
                INNER JOIN tutorias t ON r.id_tutoria = t.id_tutoria
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                LEFT JOIN (
                    SELECT te.id_tutoria,
                           GROUP_CONCAT(DISTINCT CONCAT(ue.nombre, ' ', ue.apellido)
                                        ORDER BY ue.apellido, ue.nombre SEPARATOR ', ') AS estudiantes_nombres,
                           COUNT(DISTINCT te.id_estudiante) AS cantidad_estudiantes
                    FROM tutoria_estudiantes te
                    INNER JOIN estudiantes e ON e.id_estudiante = te.id_estudiante
                    INNER JOIN usuarios ue ON ue.id_usuario = e.id_usuario
                    GROUP BY te.id_tutoria
                ) est ON est.id_tutoria = t.id_tutoria
                WHERE t.id_tutor = :id_tutor";
        $params = [':id_tutor' => (int) $id_tutor];

        if ($id_tutoria !== null && (int) $id_tutoria > 0) {
            $sql .= ' AND r.id_tutoria = :id_tutoria';
            $params[':id_tutoria'] = (int) $id_tutoria;
        }

        $sql .= ' ORDER BY r.fecha DESC, r.hora_inicio DESC, r.id_reunion DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function ultimaReunion($id_tutoria)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM reuniones
             WHERE id_tutoria = :id_tutoria
             ORDER BY fecha DESC, hora_inicio DESC
             LIMIT 1"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetch();
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        $sql = "SELECT r.*, t.id_tutoria, t.estado AS estado_tutoria, t.tipo, m.nombre_materia,
                       tt.especialidad, est_usuario.nombre AS tutor_nombre, est_usuario.apellido AS tutor_apellido
                FROM reuniones r
                INNER JOIN tutorias t ON r.id_tutoria = t.id_tutoria
                INNER JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
                INNER JOIN materias m ON t.id_materia = m.id_materia
                INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                INNER JOIN usuarios est_usuario ON tt.id_usuario = est_usuario.id_usuario
                WHERE te.id_estudiante = :id_estudiante
                ORDER BY r.fecha DESC, r.hora_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return $stmt->fetchAll();
    }

    public function obtenerPorId($id_reunion)
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.*,
                    tu.id_usuario AS id_usuario_tutor,
                    eu.id_usuario AS id_usuario_estudiante
             FROM reuniones r
             INNER JOIN tutorias t ON r.id_tutoria = t.id_tutoria
             LEFT JOIN tutores tu ON tu.id_tutor = t.id_tutor
             LEFT JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
             LEFT JOIN estudiantes e ON e.id_estudiante = te.id_estudiante
             LEFT JOIN usuarios eu ON eu.id_usuario = e.id_usuario
             WHERE r.id_reunion = :id_reunion
             LIMIT 1"
        );
        $stmt->execute([':id_reunion' => (int) $id_reunion]);

        return $stmt->fetch();
    }

    public static function extensionesPermitidas(): array
    {
        return self::EXTENSIONES_PERMITIDAS;
    }

    public static function mimesPermitidos(): array
    {
        return self::MIMES_PERMITIDOS;
    }

    public static function cumplimientos(): array
    {
        return self::CUMPLIMIENTOS;
    }

    public static function etiquetaCumplimiento($cumplimiento)
    {
        $etiquetas = [
            'completo'  => 'Cumplimiento completo',
            'parcial'   => 'Cumplimiento parcial',
            'pendiente' => 'Sin cumplimiento',
        ];

        return $etiquetas[(string) $cumplimiento] ?? 'Sin cumplimiento';
    }

    public static function etiquetaAsistencia($asistencia)
    {
        $etiquetas = ['si' => 'Asistió', 'no' => 'No asistió', 'tardanza' => 'Tardanza'];

        return $etiquetas[(string) $asistencia] ?? 'Sin registro';
    }

    public static function claseCumplimiento($cumplimiento)
    {
        $clases = [
            'completo'  => 'bg-success bg-opacity-10 text-success border-success-subtle',
            'parcial'   => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning-subtle',
            'pendiente' => 'bg-secondary bg-opacity-10 text-secondary border-secondary-subtle',
        ];

        return $clases[(string) $cumplimiento] ?? $clases['pendiente'];
    }

    public static function claseAsistencia($asistencia)
    {
        $clases = [
            'si'       => 'bg-success bg-opacity-10 text-success border-success-subtle',
            'tardanza' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning-subtle',
            'no'       => 'bg-danger bg-opacity-10 text-danger border-danger-subtle',
        ];

        return $clases[(string) $asistencia] ?? $clases['no'];
    }

    public static function reunionRealizada($reunion)
    {
        $fecha = trim((string) ($reunion['fecha'] ?? ''));
        $hora = trim((string) ($reunion['hora_fin'] ?? ''));
        if ($fecha === '') {
            return false;
        }
        $fin = $hora !== '' ? "$fecha $hora" : "$fecha 23:59:00";

        return strtotime($fin) <= time();
    }
}